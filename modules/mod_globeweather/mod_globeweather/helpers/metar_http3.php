<?php
// GlobeWeather 1.3.2  module - build 110416	metar_http3
// (c) 2010-2012 INNATO BV - www.innato.nl
// @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
// **************************************************************************
// A metar station weather fetching module for Joomla! 1.6/1.7 by Innato B.V.
// **************************************************************************

// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );

// use http3 retrieval only when all other options fail, because http3 data may be outdated
if ($_SESSION['metar_http3_option']) {
	// HTTP3 by MambWeather 2004
	$_SESSION['metar_mode_actual'] = 'HTTP3 NOK';
	$metar = '';
	
	$host = 'weather.noaa.gov';
	$location = "/pub/data/observations/metar/stations/$station.TXT";
	$request = "HTTP/1.1\r\n" .
		"If-Modified-Since: Sat, 29 Oct 1994 09:00:00 GMT\r\n" .
		"Pragma: no-cache\r\n".
		"Cache-Control: no-cache\r\n";
				
	if (@$fp = @fsockopen($host, $_SESSION['metar_http_port'], $errno, $errstr, $_SESSION['metar_http_timeout'])) {
		$request = "GET $location $request" .
			"Host: $host\r\n" .
			"Content-Type: text/html\r\n" .
			"Connection: Close\r\n\r\n";
		if ($fp) {
			@fputs($fp, $request);
			if (strpos(@fgets($fp, 1024), '200 ')) {
				do {
					$line = @fgets($fp, 1024);
				} while ($line != "\r\n");
				while ($line = @fgets($fp, 1024)) {
					$metar = $line;
				}
				$metar_retrieval_success = true;
				$_SESSION['metar_mode_actual'] = 'HTTP3 OK';
			}
			else {	// HTTP3 station not found
				$_SESSION['metar_mode_actual'] = 'HTTP3 stn not fnd';
			}
		}
		else {	// HTTP3 connection failed - may not work due to UDP socket
			$_SESSION['metar_http3_option'] = false;
			$_SESSION['metar_mode_actual'] = 'HTTP3 conn fld';
		}
	}
	else {	// HTTP3 connection failed - may not work due to UDP socket
		$_SESSION['metar_http3_option'] = false;
		$_SESSION['metar_mode_actual'] = 'HTTP3 conn fld';
	}
	@fclose($fp);
}
?>