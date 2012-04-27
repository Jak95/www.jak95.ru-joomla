<?php
// GlobeWeather 1.3.5 module - build 110429
// (c) 2010-2012 INNATO BV - www.innato.nl
// @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
// **************************************************************************
// A metar station weather fetching module for Joomla! 1.6/1.7 by Innato B.V.
// **************************************************************************

// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );

// Get the right language if it exists. If not, defaults to english.
 $lang =& JFactory::getLanguage();
 $langTag = $lang->getTag();
// $langTag = 'en-GB';	// force language - for testing

if (file_exists(JPATH_SITE.'/modules/mod_globeweather/mod_globeweather/language/' . $langTag . '.php'))
{
    include_once(JPATH_SITE.'/modules/mod_globeweather/mod_globeweather/language/' . $langTag . '.php');

}
else
{
    include_once(JPATH_SITE.'/modules/mod_globeweather/mod_globeweather/language/en-GB.php');
}

//error_reporting(E_ALL);

$station_temp		=NULL;
$location_temp		=NULL;
$marquee_output_c   =NULL;
$marquee_output_f   =NULL;

$phenomena_array = array ('#'  => '---',
                                'TS' => @$language['Thunder'],
                                'RA' => @$language['Rain'],
                                'DZ' => @$language['Rain'],
                                'SN' => @$language['Snow'],
                                'SG' => @$language['Snow'],
                                'GR' => @$language['Hail'],
                                'GS' => @$language['Hail'],
                                'PE' => @$language['Hail'],
                                'IC' => @$language['Hail'],
                                'BR' => @$language['Fog'],
                                '' => '---',
                                'FG' => @$language['Fog']);
                                
$wind_dir = array (0 => @$language['N'],
                                1 => @$language['NNE'],
                                2 => @$language['NE'],
                                3 => @$language['NEE'],
                                4 => @$language['E'],
                                5 => @$language['SEE'],
                                6 => @$language['SE'],
                                7 => @$language['SSE'],
                                8 => @$language['S'],
                                9 => @$language['SSW'],
                                10 => @$language['SW'],
                                11 => @$language['SWW'],
                                12 => @$language['W'],
                                13 => @$language['NWW'],
                                14 => @$language['NW'],
                                15 => @$language['NNW'],
                                16 => @$language['N']);                                

if(!isset($_SESSION)) {
	session_start();
}
header("Cache-control: private");

require_once  JPATH_SITE.'/modules/mod_globeweather/mod_globeweather/metar.class.php';
require_once JPATH_SITE.'/modules/mod_globeweather/mod_globeweather/Savant2.php';

// get the parameters
$station=array();
if ($params->get('station')!=='') {
	$station = explode(',',$params->get('station'));
}
else
{
	$station = explode(',','EHAM,KNYC,RJAA');
}

$location=array();
if ($params->get('location')!=='') {
	$location = explode(',',$params->get('location'));
}
else
{
	$location = explode(',','Amsterdam,New York,Tokyo');
}

if ($params->get('param_display_width_px') != '') {
	$param_display_width_px = $params->get('param_display_width_px');
	$param_display_width_px_string = 'width: '.$param_display_width_px.'px; ';
	$param_display_width_table_string = '';
}
else
{
	$param_display_width_table_string = 'width: 100%; ';
	$param_display_width_px_string = '';
	$param_display_width_px = 0;
}

if ($params->get('template')!=='') {
	$template = $params->get('template');
}
else
{
	$template = 'metric';
}

if ($params->get('weather_icon_show')!=='') {
	$weather_icon_show = $params->get('weather_icon_show');
}
else
{
	$weather_icon_show = 'yes';
}

if ($params->get('icon_set')!=='') {
	$icon_set = $params->get('icon_set');
}
else
{
	$icon_set = 'iconset1';
}

if ($params->get('offset')!=='') {
	$offset = $params->get('offset');
}
else
{
	$offset = '0';
}

if ($params->get('use_marquee')!=='') {
	$use_marquee = $params->get('use_marquee');
}
else
{
	$use_marquee = 'yes';
}

if ($params->get('marquee_with_icons')!=='') {
	$marquee_with_icons = $params->get('marquee_with_icons');
}
else
{
	$marquee_with_icons = 'no';
}
if ($marquee_with_icons == 'yes') {
	$marquee_icons_size = '20';
}
else {
	$marquee_icons_size = '0';
}

if ($params->get('details_link_type')!=='') {
	$details_link_type = $params->get('details_link_type');
}
else
{
	$details_link_type = 'text';
}

if ($params->get('station_selector_button')!=='') {
	$station_selector_button = $params->get('station_selector_button');
}
else
{
	$station_selector_button = 'black';
}

if ($params->get('metar_temp_separator')!=='') {
	$metar_temp_separator = $params->get('metar_temp_separator');
}
else
{
	$metar_temp_separator = 'yes';
}

$time_zone=array();
if ($params->get('time_zone')!=='') {
	$time_zone = explode(',',$params->get('time_zone'));
}
else
{
	$time_zone = explode(',','1,-5,9');
}

if ($params->get('metar_mode')!=='') {
	$metar_mode = $params->get('metar_mode');
}
else
{
	$metar_mode = 'HTTP1';
}

if ($params->get('metar_http_port')!=='') {
	$metar_http_port = $params->get('metar_http_port');
}
else
{
	$metar_http_port = 80;
}

if ($params->get('metar_http_timeout')!=='') {
	$metar_http_timeout = $params->get('metar_http_timeout');
}
else
{
	$metar_http_timeout = 10;
}

if ($params->get('metar_ftp_port')!=='') {
	$metar_ftp_port = $params->get('metar_ftp_port');
}
else
{
	$metar_ftp_port = 21;
}

if ($params->get('metar_ftp_timeout')!=='') {
	$metar_ftp_timeout = $params->get('metar_ftp_timeout');
}
else
{
	$metar_ftp_timeout = 10;
}

if ($params->get('metar_cache_perms_write')!=='') {
	$metar_cache_perms_write = $params->get('metar_cache_perms_write');
}
else
{
	$metar_cache_perms_write = '0757';
}
if (!is_numeric($metar_cache_perms_write)) {
	echo '<font style="background-color:#ffffff; color:#ff0000;"><strong>Error:</strong> Metar cache<br />write permissions<br />not numerical<br /><br /></font>';
	JError::raiseWarning('SOME_ERROR_CODE', JText::_('GlobeWeather error: Write permissions of metar cache are not numerical. Please correct'));
}
if(strlen($metar_cache_perms_write) == 3) {	// must be four digits
	$metar_cache_perms_write = '0'.$metar_cache_perms_write;
}

$sunrise_hour=array();
if ($params->get('sunrise_hour')!=='') {
	$sunrise_hour = explode(',',$params->get('sunrise_hour'));
}
else
{
	$sunrise_hour = explode(',','6,6,6');
}

$sunset_hour=array();
if ($params->get('sunset_hour')!=='') {
	$sunset_hour = explode(',',$params->get('sunset_hour'));
}
else
{
	$sunset_hour = explode(',','18,18,18');
}

// store some params for passing
$_SESSION['weather_icon_show'] = $weather_icon_show;
$_SESSION['metar_mode'] = $metar_mode;
$_SESSION['metar_http_port'] = $metar_http_port;
$_SESSION['metar_http_timeout'] = $metar_http_timeout;
$_SESSION['metar_ftp_port'] = $metar_ftp_port;
$_SESSION['metar_ftp_timeout'] = $metar_ftp_timeout;
$_SESSION['metar_cache_perms_write'] = $metar_cache_perms_write;
$_SESSION['use_marquee'] = $use_marquee;
$_SESSION['marquee_icons_size'] = $marquee_icons_size;
$_SESSION['details_link_type'] = $details_link_type;
$_SESSION['station_selector_button'] = $station_selector_button;
$_SESSION['param_display_width_px'] = $param_display_width_px;
$_SESSION['param_display_width_px_string'] = $param_display_width_px_string;
$_SESSION['param_display_width_table_string'] = $param_display_width_table_string;
$_SESSION['metar_temp_separator'] = $metar_temp_separator;

// check if the form has passed some value
$station_selected=JArrayHelper::getValue( $_REQUEST, 'selectbox');

if ( $station_selected<>NULL )
{
    $station_temp=strtoupper($station[$station_selected]);
	// store params for passing
	$_SESSION['station_selected'] = $station_selected;
}

if ($station_temp=='')
{
    $station_temp=strtoupper($station[0]);
    $station_selected=0;
}

// check if we have a session value stored
if ( ( @$_SESSION['station_temp'] ) && ( $station_selected==NULL ) )
{
    $station_temp=$_SESSION['station_temp'];
    $location_temp=$_SESSION['location_temp'];
    $station_selected=$_SESSION['station_selected'];
	$time_zone=$_SESSION['time_zone'];
	$sunrise_hour=$_SESSION['sunrise_hour'];
	$sunset_hour=$_SESSION['sunset_hour'];
}
else
{
	// store params for passing
    $_SESSION['station_temp']=$station_temp;
    $_SESSION['location_temp']=$location_temp;
    $_SESSION['station_selected']=$station_selected;
	$_SESSION['time_zone'] = $time_zone;
	$_SESSION['sunrise_hour'] = $sunrise_hour;
	$_SESSION['sunset_hour'] = $sunset_hour;
	$_SESSION['marquee_icons_size'] = $marquee_icons_size;
}

// check permissions for /metar_data/
$metar_cache_dir = JPATH_SITE."/modules/mod_globeweather/mod_globeweather/metar_data/";

include (JPATH_SITE."/modules/mod_globeweather/mod_globeweather/helpers/metar_perms.php");

// try to set metar cache permissions
if (!is_readable($metar_cache_dir) || !is_executable($metar_cache_dir)) {
	if (!set_dir_permissions($metar_cache_dir, '0755')) {
		echo JText::_('METARCACHEPERMISSIONERRORTEXT');
		JError::raiseWarning('SOME_ERROR_CODE', JText::_('METARCACHEPERMISSIONERRORWARNING'));
	}
}

$_SESSION['metar_cache_perms_default'] = substr(sprintf('%o', @fileperms($metar_cache_dir)), -4);

// clean up metar cache data
$dir = $metar_cache_dir;
$filename = '';
// set metar cache write permission
if($_SESSION['metar_cache_perms_default'] !== $_SESSION['metar_cache_perms_write']) {
	if(!set_dir_permissions($metar_cache_dir, $_SESSION['metar_cache_perms_write'])) {
		echo JText::_('METARCACHEWRITEPERMISSIONERRORTEXT');
		JError::raiseWarning('SOME_ERROR_CODE', JText::_('METARCACHEWRITEPERMISSIONERRORWARNING'));
	}
	elseif (!is_writable($metar_cache_dir)) {
		echo JText::_('METARCACHEWRITABLEERRORTEXT');
		JError::raiseWarning('SOME_ERROR_CODE', JText::_('METARCACHEWRITABLEERRORWARNING'));
	}
}
if($mydir = @opendir($metar_cache_dir)) {
	while(false !== ($file = readdir($mydir))) {
		if($file != "." && $file != "..") {
			if(($filename =='' || $file == $filename) && $file != "index.html" && $file != "index.htm") {
				$file_age=(time()-@filemtime($dir.$file));
				if ($file_age > 86400) {	// delete all metar cache data older than 1 day
               		@unlink($dir.$file);
				}
				if (substr($file,-13) == 'overlayed.png') {	// delete all overlayed icon cache files - needed to prevent display of cached overlay images
               		unlink($dir.$file);
				}
			}
		}
	}
	closedir($mydir);
}
// reset metar cache permissions
if($_SESSION['metar_cache_perms_write'] !== $_SESSION['metar_cache_perms_default']) {
	set_dir_permissions($metar_cache_dir, $_SESSION['metar_cache_perms_default']);
}

$cache_timeout = 900;
$weather = new weatherMetar;
$weather->cache=$cache_timeout;
$weather->offset=$offset;

// used for marquee scroll
if ($use_marquee=='yes') {
	
	$marquee = "";
	$marquee_output_c = "";
	$marquee_output_f = "";
	$count_station=0;

	foreach ($station as $station_t) {
 		$marquee=$weather->decode_metar($weather->getMetar(strtoupper($station_t)));
		$temperature_string_present = 1;
		if (!strlen(@$marquee['temperature']['temp_c'])) {
			$temperature_string_present = 0;
		}
		
    	if ($marquee_with_icons=='yes') {

			// note: below statements for c and f are the same, but they grow into
			// two different strings - for degC and degF - as these are additive string statements
			$marquee_output_c .= "&nbsp;&nbsp;&nbsp;<img height='20' align='bottom' vspace='1' border='1' src='";
			$marquee_output_f .= "&nbsp;&nbsp;&nbsp;<img height='20' align='bottom' vspace='1' border='1' src='";
			
			$sky_image = $weather->get_sky_image($count_station, $time_zone[$count_station]);

			if ($temperature_string_present) {	// we have a temperature reading
			
				// check whether metar data are too old
				$local_timestamp = time() + $offset*3600;
				$metar_readings_age_mins = floor(($local_timestamp - @$weather->decoded_metar['time'])/60);
			
				if ($metar_readings_age_mins >= 360) {
					// metar data are too old - assign overlayed icon to marquee
			
					$marquee_output_c .= broken_calendar_overlay($count_station, $icon_set, $sky_image)."' /> ";
					$marquee_output_f .= broken_calendar_overlay($count_station, $icon_set, $sky_image)."' /> ";
				
				}
				else {
					// metar data not too old - assign icon to marquee
					$marquee_output_c .= JURI::base()."modules/mod_globeweather/mod_globeweather/templates/icons/".$icon_set."/".$sky_image."' /> ";
					$marquee_output_f .= JURI::base()."modules/mod_globeweather/mod_globeweather/templates/icons/".$icon_set."/".$sky_image."' /> ";
	
				}
			}
			else {	// we have no temperature reading
				$marquee_output_c .= JURI::base()."modules/mod_globeweather/mod_globeweather/templates/icons/".$icon_set."/"."no_metar.png"."' /> ";
				$marquee_output_f .= JURI::base()."modules/mod_globeweather/mod_globeweather/templates/icons/".$icon_set."/"."no_metar.png"."' /> ";
			}
    		$marquee_output_c .= '< ';
    		$marquee_output_f .= '< ';
		}

		$marquee_output_c .= $location[$count_station].' '.@$marquee['temperature']['temp_c'];
		$marquee_output_f .= $location[$count_station].' '.@$marquee['temperature']['temp_f'];
		if (!$temperature_string_present) {
			$marquee_output_c .= "??";
			$marquee_output_f .= "??";
		}
		$marquee_output_c .= "&deg;C";
		$marquee_output_f .= "&deg;F";

 	   if (count($station) > $count_station+1 && $marquee_with_icons=='no')

		{
			$marquee_output_c .= ' +++ ';
			$marquee_output_f .= ' +++ ';
		}
		$count_station++;
	}
}

$processed_metar=$weather->decode_metar( $weather->getMetar(strtoupper($station_temp)) );

// initialize template system
$conf = array(
	'template_path' => JPATH_SITE.'/modules/mod_globeweather/mod_globeweather/templates/'.$template, 'resource_path' => ''
);
$savant = new Savant2($conf);

// assign variables to template
$sky_image = $weather->get_sky_image($station_selected, $time_zone[$station_selected]);

$_SESSION['weather_icon_status'] = "Weather data OK.";
$savant->assign('icon',JURI::base().'modules/mod_globeweather/mod_globeweather/templates/icons/'.$icon_set.'/'.$sky_image);
$savant->assign('time',@$weather->decoded_metar['time'] ? gmdate('H:i', @$weather->decoded_metar['time']) : '---');

// if metar data too old, overlay icon with broken calendar
$local_timestamp = time() + $offset*3600;
$metar_readings_age_mins = floor(($local_timestamp - @$weather->decoded_metar['time'])/60);
$orig_metar_filename = $weather->get_sky_image($station_selected, $time_zone[$station_selected]);

if($metar_readings_age_mins >= 360 && strlen(@$weather->decoded_metar['temperature']['temp_c'])) {
	/* assign overlayed icon to template */
	$_SESSION['weather_icon_status'] = "Weather data age > 6 hrs. ".$_SESSION['metar_retrieval_status'];
	$savant->assign('icon', broken_calendar_overlay($station_selected, $icon_set, $orig_metar_filename));
}

// for icon - if temperature string non-existing replace by '---'
if(!strlen(@$weather->decoded_metar['temperature']['temp_c'])) {
	$weather->decoded_metar['temperature']['temp_c'] = "---";
	$weather->decoded_metar['temperature']['temp_f'] = "---";
	// if there is no temp, there will be no dew point either
	$weather->decoded_metar['temperature']['dew_c'] = "xxx";
	$weather->decoded_metar['temperature']['dew_f'] = "xxx";
	$_SESSION['weather_icon_status'] = "No data available";
	$savant->assign('icon',JURI::base().'modules/mod_globeweather/mod_globeweather/templates/icons/'.$icon_set.'/no_metar.png');
}

$savant->assign('temperature_c',@$weather->decoded_metar['temperature']['temp_c'] ? $weather->decoded_metar['temperature']['temp_c'].' &deg;C' : '0 &deg;C');
$savant->assign('temperature_f',@$weather->decoded_metar['temperature']['temp_f'] ? $weather->decoded_metar['temperature']['temp_f'].' &deg;F' : '0 &deg;F');

if(@$weather->decoded_metar['temperature']['dew_c'] !== 'xxx') {
	$savant->assign('dew_c',@$weather->decoded_metar['temperature']['dew_c'] ? $weather->decoded_metar['temperature']['dew_c'].' &deg;C' : '0 &deg;C');
	$savant->assign('dew_f',@$weather->decoded_metar['temperature']['dew_f'] ? $weather->decoded_metar['temperature']['dew_f'].' &deg;F' : '0 &deg;F');
}
else {
	$savant->assign('dew_c','---');
	$savant->assign('dew_f','---');
}
	
$savant->assign('condition',@$phenomena_array[$weather->phenomena] ? @$phenomena_array[$weather->phenomena] : '---');
$savant->assign('location',@$location[$station_selected]);
if(strstr(@$weather->decoded_metar['wind']['deg'],"VRB")) @$weather->decoded_metar['wind']['deg']=$language['Variable'];
if ( $weather->get_wind_dir() !== '---' ) {
	$savant->assign('wind_deg',@$weather->decoded_metar['wind']['deg'].' '.@$wind_dir[$weather->get_wind_dir()]);
}
else {
	$savant->assign('wind_deg','---');
}
$savant->assign('wind_var_beg',@$weather->decoded_metar['wind']['var_beg'] ? $weather->decoded_metar['wind']['var_beg'].'&deg;' : '---');
$savant->assign('wind_var_end',@$weather->decoded_metar['wind']['var_end'] ? $weather->decoded_metar['wind']['var_end'].'&deg;' : '---');
$savant->assign('wind_knots',@$weather->decoded_metar['wind']['knots'] ? $weather->decoded_metar['wind']['knots'].' kts' : '---');
$savant->assign('wind_meters_per_second',@$weather->decoded_metar['wind']['meters_per_second'] ? $weather->decoded_metar['wind']['meters_per_second'].' m/s' : '---');
$savant->assign('wind_miles_per_hour',@$weather->decoded_metar['wind']['miles_per_hour'] ? $weather->decoded_metar['wind']['miles_per_hour'].' mph' : '---');
$savant->assign('wind_gust_knots',@$weather->decoded_metar['wind']['gust_knots'] ? $weather->decoded_metar['wind']['gust_knots'].' kts' : '---');
$savant->assign('wind_gust_meters_per_second',@$weather->decoded_metar['wind']['gust_meters_per_second'] ? $weather->decoded_metar['wind']['gust_meters_per_second'].' m/s' : '---');
$savant->assign('wind_gust_miles_per_hour',@$weather->decoded_metar['wind']['gust_miles_per_hour'] ? $weather->decoded_metar['wind']['gust_miles_per_hour'].' mph' : '---');
$savant->assign('visibility_km',@$weather->decoded_metar['visibility']['km'] ? $weather->decoded_metar['visibility']['km']." km" : '---');
$savant->assign('visibility_meter',@$weather->decoded_metar['visibility']['meter'] ? $weather->decoded_metar['visibility']['meter']." m" : '---');
$savant->assign('visibility_miles',@$weather->decoded_metar['visibility']['miles'] ? $weather->decoded_metar['visibility']['miles']." m" : '---');
$savant->assign('altimeter_hpa',@$weather->decoded_metar['altimeter']['hpa'] ? $weather->decoded_metar['altimeter']['hpa'].' hPa' : '---');
$savant->assign('altimeter_mbar',@$weather->decoded_metar['altimeter']['mbar'] ? $weather->decoded_metar['altimeter']['mbar'].' mbar' : '---');
$savant->assign('altimeter_mmhg',@$weather->decoded_metar['altimeter']['mmhg'] ? $weather->decoded_metar['altimeter']['mmhg'].' mm Hg' : '---');
$savant->assign('altimeter_inhg',@$weather->decoded_metar['altimeter']['inhg'] ? $weather->decoded_metar['altimeter']['inhg'].' in Hg' : '---');
$savant->assign('altimeter_atm',@$weather->decoded_metar['altimeter']['atm'] ? $weather->decoded_metar['altimeter']['atm'].' atm' : '---');
$savant->assign('rel_humidity',@$weather->decoded_metar['rel_humidity'] ? $weather->decoded_metar['rel_humidity']." %" : '---');

if(@$weather->decoded_metar['windchill']['windchill_c'] !== 'xxx') {
	$savant->assign('windchill_c',@$weather->decoded_metar['windchill']['windchill_c'] ? $weather->decoded_metar['windchill']['windchill_c'].' &deg;C' : '0 &deg;C');
	$savant->assign('windchill_f',@$weather->decoded_metar['windchill']['windchill_f'] ? $weather->decoded_metar['windchill']['windchill_f'].' &deg;F' : '0 &deg;F');
}
else {
	$savant->assign('windchill_c','---');
	$savant->assign('windchill_f','---');
}

if(@$weather->decoded_metar['heatindex']['heatindex_c'] !== 'xxx') {
	$savant->assign('heatindex_c',@$weather->decoded_metar['heatindex']['heatindex_c'] ? $weather->decoded_metar['heatindex']['heatindex_c'].' &deg;C' : '0 &deg;C');
	$savant->assign('heatindex_f',@$weather->decoded_metar['heatindex']['heatindex_f'] ? $weather->decoded_metar['heatindex']['heatindex_f'].' &deg;F' : '0 &deg;F');
}
else {
	$savant->assign('heatindex_c','---');
	$savant->assign('heatindex_f','---');
}
if(@$weather->decoded_metar['humidex']['humidex_c'] !== 'xxx') {
	$savant->assign('humidex_c',@$weather->decoded_metar['humidex']['humidex_c'] ? $weather->decoded_metar['humidex']['humidex_c']." &deg;C" : '0 &deg;C');
	$savant->assign('humidex_f',@$weather->decoded_metar['humidex']['humidex_f'] ? $weather->decoded_metar['humidex']['humidex_f']." &deg;F" : '0 &deg;F');
}
else {
	$savant->assign('humidex_c','---');
	$savant->assign('humidex_f','---');
}
$savant->assign('precipitation_mm',@$weather->decoded_metar['precipitation']['mm_24h'] ? $weather->decoded_metar['precipitation']['mm_24h']." mm" : '---');
$savant->assign('precipitation_in',@$weather->decoded_metar['precipitation']['in_24h'] ? $weather->decoded_metar['precipitation']['in_24h']." in" : '---');
$savant->assign('snow_in',@$weather->decoded_metar['precipitation']['snow_in'] ? $weather->decoded_metar['precipitation']['snow_in']." in" : '---');
$savant->assign('snow_mm',@$weather->decoded_metar['precipitation']['snow_mm'] ? $weather->decoded_metar['precipitation']['snow_mm']." mm" : '---');
$option_location = $location;
$selected_value = $station_selected;
$savant->assign('options', $option_location);
$savant->assign('selected', $selected_value);

// passing the language strings to the template
$savant->assign('language', $language);

if ($use_marquee=='yes')
{
	$savant->assign('scroll_info_c',$marquee_output_c);
	$savant->assign('scroll_info_f',$marquee_output_f);
} else {
    $savant->assign('scroll_info_c','');
    $savant->assign('scroll_info_f','');
}

// display template
$savant->display('template.tpl.php');

function broken_calendar_overlay($station, $icon_set, $orig_metar_filename) {
// display broken calendar icon when metar readings older than 6 hours
	$metar_iconpath = JPATH_SITE.'/modules/mod_globeweather/mod_globeweather/templates/icons/'.$icon_set.'/';
	$orig_metar_img = imagecreatefrompng($metar_iconpath.$orig_metar_filename);
	$overlay_metar_img = imagecreatefrompng($metar_iconpath."broken_calendar_overlay.png");
	$globeweather_cachedir = '/modules/mod_globeweather/mod_globeweather/metar_data/';

	$orig_metar_x = imagesx($orig_metar_img);
	$orig_metar_y = imagesy($orig_metar_img);
	$overlay_metar_x = imagesx($overlay_metar_img);
	$overlay_metar_y = imagesy($overlay_metar_img);

	/* create the new image, and scale the original into it */
	$new_metar_image = imagecreatetruecolor($orig_metar_x, $orig_metar_y);
	imagecopyresampled($new_metar_image, $orig_metar_img, 0, 0, 0, 0, $orig_metar_x, $orig_metar_y, $orig_metar_x, $orig_metar_y);
	
	// make sure that transparent images do not end up with a black background
    imagesavealpha($new_metar_image, true);
    $trans_colour = imagecolorallocatealpha($new_metar_image, 0, 0, 0, 127);
    imagefill($new_metar_image, 0, 0, $trans_colour);

	
	/* set the transparant color in the overlay, and copy it into the new image */
	imagecopymerge($new_metar_image, $overlay_metar_img, 0, $orig_metar_y - $overlay_metar_y - 2, 0, 0, $overlay_metar_x, $overlay_metar_y, 100);

	/* write new image with timestamp  */
	$globeweather_timestamp = time();
	// set metar cache write permission
	if($_SESSION['metar_cache_perms_default'] !== $_SESSION['metar_cache_perms_write']) {
		set_dir_permissions($globeweather_cachedir, $_SESSION['metar_cache_perms_write']);
	}
	@imagepng($new_metar_image, JPATH_SITE.$globeweather_cachedir.$globeweather_timestamp.'_'.$station.'_'.'calendar_overlayed.png');
	// reset metar cache permissions
	if($_SESSION['metar_cache_perms_write'] !== $_SESSION['metar_cache_perms_default']) {
		set_dir_permissions($globeweather_cachedir, $_SESSION['metar_cache_perms_default']);
	}

	/* free memory */
	imagedestroy($orig_metar_img);
	imagedestroy($overlay_metar_img);
	imagedestroy($new_metar_image);
	
	/* return icon to be assigned to template */
	return JURI::base().$globeweather_cachedir.$globeweather_timestamp.'_'.$station.'_'.'calendar_overlayed.png';
}
?>