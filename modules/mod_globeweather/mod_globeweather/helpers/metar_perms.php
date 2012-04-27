<?php
// GlobeWeather 1.3.3  module - build 110429	metar_perms
// (c) 2010-2012 INNATO BV - www.innato.nl
// @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
// **************************************************************************
// A metar station weather fetching module for Joomla! 1.6/1.7 by Innato B.V.
// **************************************************************************

// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );

function set_dir_permissions($dir, $perms) {
	// ftp connection - use for chmod and only if ftp layer is enabled
	$config =& JFactory::getConfig();
	$ftp_enabled = ( $config->getValue('ftp_enable') );
	// get the FTP credentials even if not enabled
	jimport('joomla.client.helper');
	$ftp = JClientHelper::getCredentials('ftp', true);
	// Set the ftp path - remove '//' at end if present
	$ftp_file = preg_replace ('/\/\//', '/', $ftp['root'].DS.'modules/mod_globeweather/mod_globeweather/metar_data/');
	if( $ftp_enabled ) {
		// set directory or file permissions - first try ftp then chmod
		if(@chmod_file_glob($ftp['host'],$ftp['port'],$ftp['user'],$ftp['pass'],$ftp_file,$perms) === false) {
			if(@chmod($dir, octdec($perms)) === false) {
				return false;
			}
			else return true;
		}
		else return true;
	}
	else {	// ftp layer not enabled - try chmod
		if(@chmod($dir, octdec($perms)) === false) {
			// if chmod not possible try ftp again although not enabled - one never knows
			// if not working, user can try to fill in ftp details in global config and may
			// leave ftp layer disabled
			if($ftp['host'] && $ftp['port'] && $ftp['user'] && $ftp['root']) {
				if(@chmod_file_glob($ftp['host'],$ftp['port'],$ftp['user'],$ftp['pass'],$ftp_file,$perms) === false) {
					return false;
				}
				else return true;
			}
			else {
				return false;
			}
		}
		else return true;
	}
}
?>