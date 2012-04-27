<?php
/**
 * GCalendar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GCalendar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GCalendar.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Eric Horne
 * @copyright 2009-2011 Eric Horne 
 * @since 2.2.0
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if(!empty($error)){
	echo $error;
	return;
}

if (!$gcalendar_item) {
	echo $params->get("no_event", "No events found.");
	return;
}

$targetDate = $gcalendar_item->getStartDate();
$now = false;
if ($targetDate < time()) {
	# Countdown to end of event, not currently implemented
	#$targetDate = $gcalendar_item->get_end_date(); 
	$now = true;
}

$layout = $params->get('output');
$expiryText = $params->get('output_now');
$class = "countdown";
$class .= ($now) ? "now" : "";
$mapREs = array();
$mapValues = array();

if (preg_match_all('/{{([^}]+)}}/', $layout, $mapREs)) {
	foreach ($mapREs[1] as $mapRE) {
		array_push($mapValues, call_user_func(array($gcalendar_item, 'get_' . $mapRE)));
	}

	$layout = str_replace($mapREs[0], $mapValues, $layout);
}

$objid = "countdown-" . $module->id;

GCalendarUtil::loadJQuery();
$document = &JFactory::getDocument();
$document->addScript(JURI::base(). 'components/com_gcalendar/libraries/jquery/ext/jquery.countdown.min.js');
$document->addStyleSheet(JURI::base(). 'components/com_gcalendar/libraries/jquery/ext/jquery.countdown.css');

echo "<div class=\"gcalendar_next\">\n";

$document =& JFactory::getDocument();
$calCode = "// <![CDATA[ \n";
$calCode .= "	jQuery(document).ready(function() {\n";
$calCode .= "	var targetDate; \n";
$calCode .= "	targetDate = new Date(\"".GCalendarUtil::formatDate("D,d M Y H:i:s", $targetDate)."\");\n";
$calCode .= "	jQuery('#".$objid."').countdown({until: targetDate, \n";
$calCode .= "				       description: '".str_replace('\'', '\\\'', $gcalendar_item->getTitle())."', \n";
$calCode .= " 				       layout: '".str_replace('\'', '\\\'',$layout)."', \n";
$calCode .= "				       alwaysExpire: true, expiryText: '".str_replace('\'', '\\\'',$expiryText)."', \n";
$calCode .= "				       ".$params->get('style_parameters', "format: 'dHMS'")."});\n";
$calCode .= "});\n";
$calCode .= "// ]]>\n";
$document->addScriptDeclaration($calCode);

echo "<div id=\"".$objid."\" class=\"".$class."\">". JText::_("MOD_GCALENDAR_NEXT_JSERR") . "</div>\n";
echo "</div>\n";
?>
