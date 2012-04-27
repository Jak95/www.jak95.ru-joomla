<?php
// GlobeWeather 1.3.2  module - build 110416	metar_http1
// (c) 2010-2012 INNATO BV - www.innato.nl
// @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
// **************************************************************************
// A metar station weather fetching module for Joomla! 1.6/1.7 by Innato B.V.
// **************************************************************************

// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );
		
if ($_SESSION['metar_http1_option']) {
	// HTTP1 technology by Innato 2010
	$_SESSION['metar_mode_actual'] = 'HTTP1 NOK';
	$metar = '';
	
	if ($metar = @file_get_contents('http://weather.noaa.gov/mgetmetar.php?cccc='.$station)) {
		if (preg_match("/($station [0-9]{6}Z .+)/", $metar, $matches)) {
			$metar = $matches[1];
			$metar_retrieval_success = true;
			$_SESSION['metar_mode_actual'] = 'HTTP1 OK';
		}
		else {	// HTTP1 station not found
			$_SESSION['metar_mode_actual'] = 'HTTP1 stn not fnd';
		}
	}
	else {	// HTTP1 connection failed - may not work due to UDP socket
		$_SESSION['metar_http1_option'] = false;
		$_SESSION['metar_mode_actual'] = 'HTTP1 conn fld';
	}
}
?>