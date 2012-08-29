<?php
/**
 * 			XMLRPC
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
jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT_SITE.DS.'helpers/route.php';

$controller = JController::getInstance('xmlrpc');
$view = JRequest::getCmd('view');
if(in_array($view, array('rsd', 'manifest'))){
	$task = 'display';
} else {
	$task = JRequest::getCmd('task', 'service');
}

$controller->execute($task);
$controller->redirect();
