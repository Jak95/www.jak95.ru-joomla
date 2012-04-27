<?php
/**
 * 			XMLRPC Controller
 * @version			1.0.0
 * @package			XMLRPC for Joomla!
 * @copyright			Copyright (C) 2007-2011 Joomler!.net. All rights reserved.
 * @license			http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @author			Yoshiki Kozaki : joomlers@gmail.com
 * @link			http://www.joomler.net/
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class XMLRPCController extends JController
{
	public function service()
	{
		$app = JFactory::getApplication();

		$params = JComponentHelper::getParams('com_xmlrpc');

		$plugin = $params->get('plugin', 'movabletype');

		JPluginHelper::importPlugin('xmlrpc', strtolower($plugin));
		$allCalls = $app->triggerEvent('onGetWebServices');
		if(count($allCalls) < 1){
			JError::raiseError(404, JText::_('COM_XMLRPC_SERVICE_WAS_NOT_FOUND'));
		}

		$methodsArray = array();

		foreach ($allCalls as $calls) {
			$methodsArray = array_merge($methodsArray, $calls);
		}

		@mb_regex_encoding('UTF-8');
		@mb_internal_encoding('UTF-8');

		require_once dirname(__FILE__).DS.'libraries'.DS.'phpxmlrpc'.DS.'xmlrpc.php';
		require_once dirname(__FILE__).DS.'libraries'.DS.'phpxmlrpc'.DS.'xmlrpcs.php';
		require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_content/tables');

		$xmlrpc = new xmlrpc_server($methodsArray, false);
		$xmlrpc->functions_parameters_type = 'phpvals';

		$encoding = 'UTF-8';

		$xmlrpc->xml_header($encoding);
		$GLOBALS['xmlrpc_internalencoding'] = $encoding;
		$xmlrpc->setDebug($params->get('debug', JDEBUG));
		@ini_set( 'display_errors', $params->get('display_errors', 0));

		$data = file_get_contents('php://input');

		if(empty($data)){
			JError::raiseError(403, JText::_('COM_XMLRPC_INVALID_REQUEST'));
		}

		$xmlrpc->service($data);

		jexit();
	}

	public function weblayout($preview=false)
	{
		require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

		$model = $this->getModel('Template');
		$this->addViewPath(JPATH_SITE.'/components/com_content/views');
		$view = $this->getView('Article', 'html', 'ContentView');
		$view->setModel($model, true);
		$doc = JFactory::getDocument();
		$view->assignRef('document', $doc);
		$view->addTemplatePath(JPATH_SITE.'/components/com_content/views/article/tmpl');
		$view->display();
		$view->document->setMetaData('title', '');
		return;
	}

	public function webpreview()
	{
		$this->weblayout(true);
	}
}