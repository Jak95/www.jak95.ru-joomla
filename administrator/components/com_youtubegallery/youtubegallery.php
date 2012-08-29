<?php
/**
 * Youtube Gallery Joomla! 2.5 Native Component
 * @version 2.2.4
 * @author DesignCompass corp <admin@designcompasscorp.com>
 * @link http://www.designcompasscorp.com
 * @license GNU/GPL
 **/


// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import joomla controller library
jimport('joomla.application.component.controller');
 
 
 
 
$controllerName = JRequest::getCmd( 'view', 'gallerylist' );


switch($controllerName)
{
	
	case 'gallerylist':
		
		JSubMenuHelper::addEntry(JText::_('Galleries'), 'index.php?option=com_youtubegallery&view=gallerylist', true);
		JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_youtubegallery&view=categories', false);
	break;

	case 'categories':
		
		JSubMenuHelper::addEntry(JText::_('Galleries'), 'index.php?option=com_youtubegallery&view=gallerylist', false);
		JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_youtubegallery&view=categories', true);
	break;

}
 
//




//require_once( JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php' );


//$controllerName = 'YoutubeGalleryController'.$controllerName;
//$controller	= new $controllerName( );

$controller = JController::getInstance('youtubeGallery');

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();

?>