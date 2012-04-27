<?php
/*
 * v1.0.0
 * Friday Sep-02-2011
 * @component JooComments
 * @copyright Copyright (C) Abhiram Mishra www.bullraider.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//Require the submenu for component
jimport( 'joomla.application.component.model' );
jimport('joomla.utilities.date');
jimport('joomla.application.component.controller');

if(!JRequest::getCmd('view')) JRequest::setVar('view', 'comments');
$controller = JRequest::getCmd('view');

require_once (JPATH_SITE.DS.'components'.DS.'com_joocomments'.DS.'controller.php');
$view = $controller;
if($controller) {
	$path = JPATH_SITE.DS.'components'.DS.'com_joocomments'.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}
// Create the controller
$classname	= 'JooCommentsController'.ucfirst($controller);
$controller = new $classname( );

$controller->_basePath = JPATH_SITE.DS.'components'.DS.'com_joocomments';
$controller->_path['view'][0] = JPATH_SITE.DS.'components'.DS.'com_joocomments'.DS.'views'.DS;


$task = JRequest::getVar('task', null, 'default', 'cmd');

$controller->execute( $task );


// Redirect if set by the controller
$controller->redirect();
?>