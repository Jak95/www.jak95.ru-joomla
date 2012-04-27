<?php
// GlobeWeather 1.3  module - build 110511	template.tpl
// (c) 2010-2012 INNATO BV - www.innato.nl
// @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
// **************************************************************************
// A metar station weather fetching module for Joomla! 1.6/1.7 by Innato B.V.
// **************************************************************************

// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!--GlobeWeather for Joomla! 1.6/1.7 - 1.3.5 met - 15 Jan 2012 | 00:00 - <?php echo $_SESSION['metar_mode'].'/'; if(isset($_SESSION['metar_mode_actual'])) { echo $_SESSION['metar_mode_actual']; }?> - by www.innato.nl -->

<?php
$doc =& JFactory::getDocument();
$doc->addStyleSheet( JURI::root(true).'/modules/mod_globeweather/mod_globeweather/css/globeweather.css' );
?>

<script type="text/javascript" language="javascript">
<!--
var dom = (document.getElementById && !document.all)? 1: 0;
function show_hide(the_id) {
	var obj = (dom)? document.getElementById(the_id): document.all[the_id];
	if(obj.style.visibility == "hidden"){ // show
		obj.style.visibility = "visible";
		obj.style.display = "block"; 
	}
	else { // hide
		obj.style.visibility = "hidden";
		obj.style.display = "none";
	}
}
-->
</script>

<?php
// only include script if needed
if ($_SESSION['use_marquee'] == 'yes') { ?>
	<script type="text/javascript">
<!--// 
	// Ticker variables
	var scrollingticker_content_array = new Array(
		"<?php echo ($scroll_info_c); ?>"
	);

	var scrollingticker_separator = "+++";		// separator between content array items

	var scrollingticker_font_size_px = 11;
	var scrollingticker_font_family = "arial, helvetica, sans-serif";
	var scrollingticker_font_colour = "#000000";
	var scrollingticker_bg_colour = "#cccccc";
	var scrollingticker_mouseover_colour = "#cccccc";

	var scrollingticker_width_px = <?php echo $_SESSION['param_display_width_px'];?>;
	var scrollingticker_margin_top_px = 0;
	var scrollingticker_margin_bottom_px = 0;

	var scrollingticker_img_height_px = <?php echo (int)$_SESSION['marquee_icons_size'];?>;
	var scrollingticker_height_px = Math.max(scrollingticker_font_size_px, scrollingticker_img_height_px) + 7;

	var scrollingticker_scroll_interval = 75;			// smaller number scrolls faster

	var scrollingticker_interval, scrollingticker_index;
	var scrollingticker_content_items = new Array();
//-->
	</script>
<?php
}
?>

<?php
if($_SESSION['metar_temp_separator'] == 'yes'){
	$metar_temp_separator_style = '1px dotted';
}
else {
	$metar_temp_separator_style = '';
}
?>

<table align="center" style="<?php echo $_SESSION['param_display_width_table_string'];?>font-size: 11px;" border="0">
<?php if($_SESSION['weather_icon_show'] == 'yes'){
	echo "<tr style=\"text-align:center;\"><td colspan=\"2\" style=\"text-align:center; background-color:#ffffff;\"><img src=\"$icon\" alt=\"".$_SESSION['weather_icon_status']."\"/></td></tr>";
}
?>
<tr style="text-align:center;"><td style="text-align:center; background-color:#666666;" colspan="2"><div style="<?php echo $_SESSION['param_display_width_px_string'];?>font-family: arial, helvetica, sans-serif; font-size: medium; color: #fff; border-bottom: <?php echo $metar_temp_separator_style;?>;"><?php echo $location;?></div>
<div style="font-family: arial, helvetica, sans-serif; font-size: medium; font-weight: bold; color: #ffff00;"><?php echo $temperature_c;?></div></td></tr>

<?php if ($_SESSION['use_marquee'] == 'yes') { ?>
	<tr>
	<td colspan="2">
	<script language="JavaScript" type="text/javascript" src="<?php echo(JURI::root(true))?>/modules/mod_globeweather/mod_globeweather/helpers/scrollingticker.js"></script>
	</td>
	</tr>
<?php
}
?>

<tr>
	<td colspan="2" style="text-align:center;">
	<?php
	if ($_SESSION['details_link_type'] == 'text') { ?>
		<a href="javascript:show_hide('details')">[<?php echo $language['Details'];?>]</a>
		<?php
	}
	elseif ($_SESSION['details_link_type'] == 'arrow_down_white') { ?>
		<span class="module_globeweather_details_button_png"><a href="javascript:show_hide('details')"><img src="<?php echo JURI::root(true)?>/modules/mod_globeweather/mod_globeweather/templates/icons/details_white.png" alt="Weather details"/></a>
    	</span>
		<?php
	}
	else {?>
		<span class="module_globeweather_details_button_png"><a href="javascript:show_hide('details')"><img src="<?php echo JURI::root(true)?>/modules/mod_globeweather/mod_globeweather/templates/icons/details.png" alt="Weather details"/></a>
    	</span>
    	<?php
	} ?>
	</td>
</tr>
</table>

<div id="details" style="visibility:hidden; display:none;">

<table align="center" style="<?php echo $_SESSION['param_display_width_table_string'];?>font-size: 11px;" border="0">
<tr style="text-align:center;">
<td colspan="2">

<?php
if ($_SESSION['param_display_width_px_string']) { ?>
	<div style="<?php echo $_SESSION['param_display_width_px_string'];?>"></div>
	<?php
}
?>

</td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Time'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $time;?></td>
</tr>
<tr>
    	<td height="3" colspan="2"></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Condition'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $condition;?></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Feels_Like'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $windchill_c;?></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Dew_Point'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $dew_c;?></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Wind_Direction'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $wind_deg;?></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Variable_Wind'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $wind_var_beg.'/'.$wind_var_end;?></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Wind_Speed'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $wind_meters_per_second;?></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Wind_Gust'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $wind_gust_meters_per_second;?></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Visibility'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $visibility_km;?></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Pressure'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $altimeter_hpa;?></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Humidity'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $rel_humidity;?></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Humidity_Index'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $humidex_c;?></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Heat_Index'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $heatindex_c;?></td>
</tr>
<tr>
<td style="background-color:#cccccc; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $language['Precipitation'];?></td><td style="background-color:#aaaaaa; text-align:left; padding-left:2px; font-family: arial, helvetica, sans-serif; color: #000000;"><?php echo $precipitation_mm;?></td>
</tr>
    
<?php
// This module has been made available to you free-of-charge. In return, the below credit line
// must remain intact and unchanged. We believe this is only fair and it helps us to keep our
// software development going.
?>
<tr>
	<td colspan="2" style="text-align:center">
	<div style="font-family: arial, helvetica, sans-serif; font-size: xx-small;"><a href="<?php echo htmlspecialchars('http://www.innato.nl/index.php?option=com_content&view=article&id=52:free-downloads&catid=36&Itemid=60#module_globeweather_J16')?> " target="_blank">GlobeWeather by Innato</a></div>
	</td>
</tr>
    
<tr style="text-align:center">
	<td colspan="2" style="text-align:center">
	<form action="<?php $_SERVER['REQUEST_URI']?>" method="post">
		<select name="selectbox">
			<?php $this->plugin('options', $options, $selected); ?>
		</select>
		&nbsp;
        <?php
		if ($_SESSION['station_selector_button'] === 'black') { ?>
        <input type="image" src="modules/mod_globeweather/mod_globeweather/templates/icons/submit.png" style="vertical-align:middle; height:18px;" name="submit" alt="<?php echo $language['Change']?>"/>
        <?php
		}
		else { ?>
        <input type="image" src="modules/mod_globeweather/mod_globeweather/templates/icons/submit_white.png" style="vertical-align:middle; height:18px;" name="submit" alt="<?php echo $language['Change']?>"/>
        <?php
		} ?>		
	</form>
</td>
</tr>
</table>
</div>

<?php
unset($_SESSION['metar_ftp_option']);
unset($_SESSION['metar_http1_option']);
unset($_SESSION['metar_http2_option']);
unset($_SESSION['metar_http3_option']);
?>