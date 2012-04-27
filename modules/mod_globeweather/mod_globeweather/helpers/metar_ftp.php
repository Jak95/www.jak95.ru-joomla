<?php
// GlobeWeather 1.3.2  module - build 110416	metar_ftp
// (c) 2010-2012 INNATO BV - www.innato.nl
// @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
// **************************************************************************
// A metar station weather fetching module for Joomla! 1.6/1.7 by Innato B.V.
// **************************************************************************

// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );

if ($_SESSION['metar_ftp_option']) {	
	$_SESSION['metar_mode_actual'] = 'FTP NOK';
	// compose the requesting site url as the anynomous ftp password
	if($url = JURI::base()) {	
		$requestor_url = substr(htmlspecialchars( JRoute::_($url) ),7);
		$requestor_url = substr($requestor_url,0,strlen($requestor_url)-1);
	}
	else {
		$requestor_url = 'globeweather';
	}

	// initialise variables
	$metar = '';
	$ftp_host = "tgftp.nws.noaa.gov";
	$ftp_user = "anonymous";
	$ftp_pass = $requestor_url;
	$remote_file = "/data/observations/metar/stations/".$station.".TXT";
			
	// delete the metar cache file but first make sure it is closed
	$fp = @fopen($metar_cache, 'w');
	@fclose($fp);
	@unlink($metar_cache);

	// establish basic connection
	$conn_id = @ftp_connect($ftp_host, $_SESSION['metar_ftp_port'], $_SESSION['metar_ftp_timeout']);

	// login with username and password
	$login_result = @ftp_login($conn_id, $ftp_user, $ftp_pass);

	// check the connection
	if (($conn_id) && ($login_result)) {
		// check if remote file exists
		if (@ftp_size($conn_id, $remote_file) <> -1) {
			$_SESSION['metar_mode_actual'] = 'FTP file exist';

			// download $remote_file and save to local file $metar_cache and exit if fails
			if (@ftp_get($conn_id, $metar_cache, $remote_file, FTP_ASCII)) {
					
				// read the local $metar_cache file and store into $metar
				$fp 	= @fopen($metar_cache, "r");
				$metar	= @fread($fp, filesize($metar_cache));
				@fclose($fp);

				// strip first 17 characters of raw metar string (these are date and time)
				$metar = substr($metar, 17);
				$metar_retrieval_success = true;
				$_SESSION['metar_mode_actual'] = 'FTP OK';
			}
			else {
				$_SESSION['metar_mode_actual'] = 'FTP rtrvl failed';
				$_SESSION['metar_ftp_option'] = false;
			}
		}
		else {
			$_SESSION['metar_mode_actual'] = 'FTP file nexist';
		}
	}
	else {
		$_SESSION['metar_mode_actual'] = 'FTP conn fld';
		$_SESSION['metar_ftp_option'] = false;
	}
	@ftp_close($conn_id);
}
?>