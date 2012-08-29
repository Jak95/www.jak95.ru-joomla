<?php
/**
 * Mobile Joomla!
 * http://www.mobilejoomla.com
 *
 * @version		1.1.0
 * @license		GNU/GPL v2 - http://www.gnu.org/licenses/gpl-2.0.html
 * @copyright	(C) 2008-2012 Kuneri Ltd.
 * @date		June 2012
 */
defined('_JEXEC') or die('Restricted access');

function _initStatus()
{
	JError::setErrorHandling(E_ERROR, 'Message');
	@set_time_limit(1200);
	@ini_set('max_execution_time', 1200);
}
function _sendStatus()
{
	$msg = array();
	foreach(JError::getErrors() as $error)
		if($error->get('level'))
			$msg[] = $error->get('message');
	if(count($msg))
		$msg = '<p>'.implode('</p><p>', $msg).'</p>';
	else
		$msg = 'ok';
	echo $msg;
	jexit();
}

	jimport('joomla.plugin.helper');
	jimport('joomla.installer.helper');
	jimport('joomla.installer.installer');
	$app = JFactory::getApplication();

	_initStatus();
	$dir = $app->getUserState( "com_mobilejoomla.scientiaupdatedir", false );
	if($dir)
	{
		$installer = new JInstaller();
		$installer->install($dir);
		$app->setUserState( "com_mobilejoomla.scientiaupdatedir", false );
		JFolder::delete($dir);
		global $isJoomla15;
		if($isJoomla15)
		{
			require_once JPATH_ROOT.DS.'plugins'.DS.'mobile'.DS.'scientia'.DS.'scientia_helper.php';
			ScientiaHelper::installDatabase();
			ScientiaHelper::enablePlugin();
		}
	}
	else
		JError::raiseWarning(1, JText::_('COM_MJ__UPDATE_UNKNOWN_PATH'));
	_sendStatus();
