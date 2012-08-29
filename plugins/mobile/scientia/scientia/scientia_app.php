<?php
/**
 * Mobile Joomla! ScientiaMobile DBI extension
 * http://www.mobilejoomla.com
 *
 * @version		1.1-2012.03.26
 * @license		AGPL
 * @copyright	(C) 2008-2012 Kuneri Ltd.
 * @date		June 2012
 */

define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))).DS.'administrator' );
require_once( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'framework.php' );
	
$app = new ScientiaApp();
$task = JRequest::getCmd('task', 'display');
$app->execute($task);


class ScientiaApp
{
	function execute($task)
	{
		$app =& JFactory::getApplication('administrator'); 
		$user =& JFactory::getUser(); 
		if(!$user->authorize('login', 'administrator'))
			exit(0);
		$lang =& JFactory::getLanguage();
		$lang->load('plg_mobile_scientia');

		$task = strtolower($task);
		if(!method_exists($this, $task))
			return false;
		return $this->$task();
	}

	function install()
	{
		require_once( JPATH_ROOT .DS.'plugins'.DS.'mobile'.DS.'scientia'.DS.'scientia_helper.php' );

		ScientiaHelper::disablePlugin();
		$status = ScientiaHelper::installDatabase();
		if($status)
		{
			ScientiaHelper::disablePlugin('amdd');
			ScientiaHelper::enablePlugin();
		}

		$this->redirect();
	}

	function drop()
	{
		require_once( JPATH_ROOT .DS.'plugins'.DS.'mobile'.DS.'scientia'.DS.'scientia_helper.php' );

		ScientiaHelper::disablePlugin();
		ScientiaHelper::enablePlugin('amdd');
		ScientiaHelper::dropDatabase();

		$this->redirect();
	}

	function display()
	{
		header('Content-type: application/x-javascript');

		$checks = array();
		if(version_compare(phpversion(), '5.0', '<'))
			$checks[] = JText::_('PLG_MOBILE_SCIENTIA__PHP5_ONLY');
		if(!class_exists('mysqli') || !function_exists('mysqli_connect'))
			$checks[] = JText::_('PLG_MOBILE_SCIENTIA__MYSQLI_LIBRARY');
		if(count($checks))
		{
			?>document.write("<b><?php
				echo JText::_('PLG_MOBILE_SCIENTIA__INCOMPATIBLE');
				?> (<?php
				echo implode(', ', $checks);
				?>).</b>");<?php
			return;
		}

		require_once( JPATH_ROOT .DS.'plugins'.DS.'mobile'.DS.'scientia'.DS.'scientia_helper.php' );

		$isInstalled = ScientiaHelper::isInstalled();
		if($isInstalled)
		{
?>document.write('<div style="margin-top:1em"><a href="../plugins/mobile/scientia/scientia_app.php?task=drop" style="padding:0.5em;border:1px solid #999"><?php echo JText::_('PLG_MOBILE_SCIENTIA__DROP_DATABASE'); ?></a></div>');
<?php
		}
		else
		{
?>document.write('<div style="margin-top:1em"><a href="../plugins/mobile/scientia/scientia_app.php?task=install" style="padding:0.5em;border:1px solid #999"><?php echo JText::_('PLG_MOBILE_SCIENTIA__INSTALL_DATABASE'); ?></a></div>');
<?php
		}
	}
	
	function redirect()
	{
		$db = JFactory::getDBO();
		$query = "SELECT id FROM #__plugins WHERE element = 'scientia' AND folder = 'mobile'";
		$db->setQuery($query);
		$id = $db->loadResult();

		$app = JFactory::getApplication();
		$app->redirect(JURI::root().'../../../administrator/index.php?option=com_plugins&view=plugin&client=site&task=edit&cid[]='.$id);
	}
}
