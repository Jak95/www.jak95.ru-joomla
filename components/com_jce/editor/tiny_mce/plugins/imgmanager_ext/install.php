<?php
/**
* @package JCE Image Manager Extened
* @copyright Copyright (C) 2005 - 2010 Ryan Demmer. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see licence.txt
* JCE Image Manager Extened is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined('_JEXEC') or die('Restricted access');

function com_install() {
    $mainframe =JFactory::getApplication();
	
	jimport('joomla.filesystem.folder');
	// Initialize variables
	jimport('joomla.client.helper');
	$FTPOptions = JClientHelper::getCredentials('ftp');
	
	$language = JFactory::getLanguage();		
	$language->load( 'com_jce_imgmanager_ext', JPATH_SITE );
	
	$cache = $mainframe->getCfg('tmp_path');
	
	// Check for tmp folder
	if( !JFolder::exists( $cache ) ){
		// Create if does not exist
		if( !JFolder::create( $cache ) ){
			$mainframe->enqueueMessage( WFText::_('WF_IMGMANAGER_EXT_NO_CACHE_DESC'), 'error' );
		}
	}
	// Check if folder exists and is writable or the FTP layer is enabled	
	if( JFolder::exists( $cache ) && ( is_writable( $cache ) || $FTPOptions['enabled'] == 1 ) ){
		$mainframe->enqueueMessage( WFText::_('WF_IMGMANAGER_EXT_CACHE_DESC') );
	}else{
		$mainframe->enqueueMessage( WFText::_('WF_IMGMANAGER_EXT_NO_CACHE_DESC'), 'error' );
	}
	// Check for GD
	if( !function_exists( 'gd_info' ) ){
		$mainframe->enqueueMessage( WFText::_('WF_IMGMANAGER_EXT_NO_GD_DESC'), 'error' );
	}else{
		$info = gd_info();
		$mainframe->enqueueMessage( WFText::_('WF_IMGMANAGER_EXT_GD_DESC') . ' - ' . $info['GD Version'] );
	}
}