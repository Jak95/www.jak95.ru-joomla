<?php
// GlobeWeather 1.3.2  module - build 110416	metar_http2
// (c) 2010-2012 INNATO BV - www.innato.nl
// @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
// **************************************************************************
// A metar station weather fetching module for Joomla! 1.6/1.7 by Innato B.V.
// **************************************************************************

// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );
	
if ($_SESSION['metar_http2_option']) {
	// HTTP2 technology by Innato 2010
	$_SESSION['metar_mode_actual'] = 'HTTP2 NOK';
	$metar = '';

	$host = 'weather.noaa.gov';
	$location = "/mgetmetar.php?cccc=$station";
	$request = "HTTP/1.1\r\n" .
		"If-Modified-Since: Fri, 01 Jan 2010 09:00:00 GMT\r\n" .
		"Pragma: no-cache\r\n" .
		"Cache-Control: no-cache\r\n";
				
	if (@$fp = @fsockopen($host, $_SESSION['metar_http_port'], $errno, $errstr, $_SESSION['metar_http_timeout'])) {
		$request = "POST $location $request" .
			"Host: $host\r\n" .
			"Content-Type: text/html; charset=utf-8\r\n" .
			"Content-Length: 9\r\n\r\n" .
			"Connection: Close\r\n\r\n";
		if ($fp) {
			@fputs($fp, $request);
			if (strpos(@fgets($fp, 1024), '200 ')) {
				do {
					$line = @fgets($fp, 1024);
				} while ($line != "\r\n");
				while ($line = @fgets($fp, 1024)) {
					$line = strip_tags($line);
					if (preg_match("/($station [0-9]{6}Z .+)/", $line, $matches)) {
						$metar = $matches[1];
						$metar_retrieval_success = true;
						$_SESSION['metar_mode_actual'] = 'HTTP2 OK';
					}
				}
				if ($metar == '') {	// HTTP2 station not found
					$_SESSION['metar_mode_actual'] = 'HTTP2 stn not fnd';
				}
			}
			else {	// HTTP2 connection failed - may not work due to UDP socket
				$_SESSION['metar_http2_option'] = false;
				$_SESSION['metar_mode_actual'] = 'HTTP2 conn fld';
			}
		}
	}
	else {	// HTTP2 connection failed - may not work due to UDP socket
		$_SESSION['metar_http2_option'] = false;
		$_SESSION['metar_mode_actual'] = 'HTTP2 conn fld';
	}
	@fclose($fp);
}
?>