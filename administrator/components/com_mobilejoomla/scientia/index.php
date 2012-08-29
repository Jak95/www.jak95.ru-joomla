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

define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))) );
require_once( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$app = JFactory::getApplication('administrator');
$app->initialise();

$user = JFactory::getUser();

global $isJoomla15;
$isJoomla15 = (substr(JVERSION,0,3) == '1.5');

if($isJoomla15)
{
	if(!$user->authorize('login', 'administrator'))
		exit(0);
}
else
{
	if(!$user->authorise('core.login.admin'))
		exit(0);
}

$lang = JFactory::getLanguage();
$lang->load('com_mobilejoomla');

global $mootools;
$mootools = '../../../../media/system/js/' . ($isJoomla15 ? 'mootools.js' : 'mootools-core.js');

$action = JRequest::getCmd('action', 'init');
$file = dirname(__FILE__).DS.$action.'.php';
if(!file_exists($file))
	exit(0);

require($file);
