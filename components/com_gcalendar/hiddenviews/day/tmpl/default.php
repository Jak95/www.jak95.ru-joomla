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
 * @author Allon Moritz
 * @copyright 2007-2011 Allon Moritz
 * @since 2.2.0
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.parameter');

$params = new JParameter('');
$itemID = null;
foreach($this->calendars as $calendar) {
	$itemID = GCalendarUtil::getItemId($calendar->id);
}
if(!empty($itemID)){
	$component	= &JComponentHelper::getComponent('com_gcalendar');
	$menu = &JSite::getMenu();
	$params = $menu->getParams($itemID);
	echo "<table class=\"gcalendar-table\"><tr><td valign=\"middle\">\n";
	echo '<a href="'.JRoute::_('index.php?option=com_gcalendar&Itemid='.$itemID)."\">\n";
	echo "<img id=\"prevBtn_img\" height=\"16\" border=\"0\" width=\"16\" alt=\"backlink\" src=\"media/com_gcalendar/images/back.png\"/>\n";
	echo "</a></td><td valign=\"middle\">\n";
	echo '<a href="'.JRoute::_('index.php?option=com_gcalendar&Itemid='.$itemID).'">'.JText::_( 'COM_GCALENDAR_GCALENDAR_VIEW_CALENDAR_BACK_LINK' )."</a>\n";
	echo "</td></tr></table>\n";
}

JHTML::_('behavior.mootools');
GCalendarUtil::loadJQuery();
$document = &JFactory::getDocument();
$document->addStyleSheet(JURI::base(). 'components/com_gcalendar/views/gcalendar/tmpl/gcalendar.css' );
$document->addScript(JURI::base(). 'components/com_gcalendar/libraries/fullcalendar/fullcalendar.min.js' );
$document->addStyleSheet(JURI::base().'components/com_gcalendar/libraries/fullcalendar/fullcalendar.css');
$document->addScript(JURI::base().'components/com_gcalendar/libraries/jquery/ui/jquery-ui.custom.min.js');
$document->addScript(JURI::base().'components/com_gcalendar/libraries/jquery/ext/jquery.ba-hashchange.min.js');
$document->addScript(JURI::base().'components/com_gcalendar/libraries/jquery/ext/jquery.qtip-1.0.0.min.js');
$document->addStyleDeclaration("#ui-datepicker-div { z-index: 15; }");

$theme = $params->get('theme', '');
if(JRequest::getVar('theme', null) != null)
$theme = JRequest::getVar('theme', null);
if(!empty($theme))
$document->addStyleSheet(JURI::base().'components/com_gcalendar/libraries/jquery/themes/'.$theme.'/jquery-ui.custom.css');
else
$document->addStyleSheet(JURI::base().'components/com_gcalendar/libraries/jquery/themes/ui-lightness/jquery-ui.custom.css');

$calsSources = "		eventSources: [\n";
foreach($this->calendars as $calendar) {
	$cssClass = "gcal-event_gccal_".$calendar->id;
	$calsSources .= "			'".JRoute::_('index.php?option=com_gcalendar&view=jsonfeed&format=raw&gcid='.$calendar->id)."',\n";
	$color = GCalendarUtil::getFadedColor($calendar->color);
	$document->addStyleDeclaration(".".$cssClass.",.fc-agenda ".$cssClass." .fc-event-time, .".$cssClass." a, .".$cssClass." div{background-color: ".$color." !important; border-color: #".$calendar->color."; color: white;}");
}
$calsSources = trim($calsSources, ",\n");
$calsSources .= "		],\n";

$daysLong = "[";
$daysShort = "[";
$daysMin = "[";
$monthsLong = "[";
$monthsShort = "[";
for ($i=0; $i<7; $i++) {
	$daysLong .= "'".GCalendarUtil::dayToString($i, false)."'";
	$daysShort .= "'".GCalendarUtil::dayToString($i, true)."'";
	$daysMin .= "'".substr(GCalendarUtil::dayToString($i, true), 0, 2)."'";
	if($i < 6){
		$daysLong .= ",";
		$daysShort .= ",";
		$daysMin .= ",";
	}
}
for ($i=1; $i<=12; $i++) {
	$monthsLong .= "'".GCalendarUtil::monthToString($i, false)."'";
	$monthsShort .= "'".GCalendarUtil::monthToString($i, true)."'";
	if($i < 12){
		$monthsLong .= ",";
		$monthsShort .= ",";
	}
}
$daysLong .= "]";
$daysShort .= "]";
$daysMin .= "]";
$monthsLong .= "]";
$monthsShort .= "]";

$calCode = "// <![CDATA[ \n";
$calCode .= "jQuery(document).ready(function(){\n";
$calCode .= "	var today = new Date();\n";
$calCode .= "	var tmpYear = today.getFullYear();\n";
$calCode .= "	var tmpMonth = today.getMonth();\n";
$calCode .= "	var tmpDay = today.getDate();\n";
$calCode .= "	var vars = window.location.hash.replace(/&amp;/gi, \"&\").split(\"&\");\n";
$calCode .= "	for ( var i = 0; i < vars.length; i++ ){\n";
$calCode .= "		if(vars[i].match(\"^#year\"))tmpYear = vars[i].substring(6);\n";
$calCode .= "		if(vars[i].match(\"^month\"))tmpMonth = vars[i].substring(6)-1;\n";
$calCode .= "		if(vars[i].match(\"^day\"))tmpDay = vars[i].substring(4);\n";
$calCode .= "	}\n";
$calCode .= "   jQuery('#gcalendar_component_day').fullCalendar({\n";
$calCode .= "		header: {\n";
$calCode .= "			left: '',\n";
$calCode .= "			center: 'title',\n";
$calCode .= "			right: ''\n";
$calCode .= "		},\n";
$calCode .= "		year: tmpYear,\n";
$calCode .= "		month: tmpMonth,\n";
$calCode .= "		date: tmpDay,\n";
$calCode .= "		defaultView: 'agendaDay',\n";
$calCode .= "		editable: false, theme: ".(!empty($theme)?'true':'false').",\n";
$calCode .= "		titleFormat: { day: '".Fullcalendar::convertFromPHPDate($params->get('titleformat_day', 'l, M j, Y'))."'},\n";
$calCode .= "		monthNames: ".$monthsLong.",\n";
$calCode .= "		monthNamesShort: ".$monthsShort.",\n";
$calCode .= "		dayNames: ".$daysLong.",\n";
$calCode .= "		dayNamesShort: ".$daysShort.",\n";
if($params->get('calendar_height', 0) > 0){
	$calCode .= "		contentHeight: ".$params->get('calendar_height', 0).",\n";
}
$calCode .= "		timeFormat: { day: '".Fullcalendar::convertFromPHPDate($params->get('timeformat_day', 'H:i{ - H:i}'))."'},\n";
$calCode .= "		columnFormat: { month: 'ddd', week: 'ddd d', day: 'dddd d'},\n";
$calCode .= "		axisFormat: '".Fullcalendar::convertFromPHPDate($params->get('axisformat', 'H:i'))."',\n";
$calCode .= "		allDayText: '".JText::_( 'COM_GCALENDAR_GCALENDAR_VIEW_ALL_DAY' )."',\n";
$calCode .= $calsSources;
$calCode .= "		eventRender: function(event, element) {\n";
$calCode .= "			if (event.description)\n";
$calCode .= "				jQuery(element).qtip({\n";
$calCode .= "					content: event.description,\n";
$calCode .= "					position: {\n";
$calCode .= "						corner: {\n";
$calCode .= "							target: 'topLeft',\n";
$calCode .= "							tooltip: 'bottomLeft'\n";
$calCode .= "						}\n";
$calCode .= "					},\n";
$calCode .= "					border: {\n";
$calCode .= "						radius: 4,\n";
$calCode .= "						width: 3\n";
$calCode .= "					},\n";
$calCode .= "					style: { name: 'cream', tip: 'bottomLeft' }\n";
$calCode .= "				});\n";
$calCode .= "		},\n";
$calCode .= "		eventClick: function(event) {\n";
if($params->get('show_event_as_popup', 1) == 1){
	$calCode .= "			if (event.url) {\n";
	$calCode .= "				jQuery('<iframe src=\"'+event.url+'&tmpl=component\" />').dialog({\n";
	$calCode .= "					width: 650,\n";
	$calCode .= "					height: 500,\n";
	$calCode .= "					modal: true,\n";
	$calCode .= "					autoResize: true\n";
	$calCode .= "				}).width(630).height(480);\n";
	$calCode .= "				return false;}\n";
}
$calCode .= "		},\n";
$calCode .= "		loading: function(bool) {\n";
$calCode .= "			if (bool) {\n";
$calCode .= "				jQuery('#gcalendar_component_day_loading').show();\n";
$calCode .= "			}else{\n";
$calCode .= "				jQuery('#gcalendar_component_day_loading').hide();\n";
$calCode .= "			}\n";
$calCode .= "		}\n";
$calCode .= "	  });\n";
$calCode .= "	jQuery(window).bind( 'hashchange', function(){\n";
$calCode .= "		var today = new Date();\n";
$calCode .= "		var tmpYear = today.getFullYear();\n";
$calCode .= "		var tmpMonth = today.getMonth();\n";
$calCode .= "		var tmpDay = today.getDate();\n";
$calCode .= "		var vars = window.location.hash.replace(/&amp;/gi, \"&\").split(\"&\");\n";
$calCode .= "		for ( var i = 0; i < vars.length; i++ ){\n";
$calCode .= "			if(vars[i].match(\"^#year\"))tmpYear = vars[i].substring(6);\n";
$calCode .= "			if(vars[i].match(\"^month\"))tmpMonth = vars[i].substring(6)-1;\n";
$calCode .= "			if(vars[i].match(\"^day\"))tmpDay = vars[i].substring(4);\n";
$calCode .= "		}\n";
$calCode .= "		var date = new Date(tmpYear, tmpMonth, tmpDay,0,0,0);\n";
$calCode .= "		var d = jQuery('#gcalendar_component_day').fullCalendar('getDate');\n";
$calCode .= "		if(date.getFullYear() != d.getFullYear() || date.getMonth() != d.getMonth() || date.getDate() != d.getDate())\n";
$calCode .= "			jQuery('#gcalendar_component_day').fullCalendar('gotoDate', date);\n";
$calCode .= "	});\n";
$calCode .= "});\n";
$calCode .= "// ]]>\n";
$document->addScriptDeclaration($calCode);

echo "<div id='gcalendar_component_day_loading' style=\"text-align: center;\"><img src=\"".JURI::base() . "media/com_gcalendar/images/ajax-loader.gif\" alt=\"loader\" /></div>";
echo "<div id='gcalendar_component_day'></div>";
echo "<div style=\"text-align:center;margin-top:10px\" id=\"gcalendar_powered\"><a href=\"http://g4j.laoneo.net\">Powered by GCalendar</a></div>\n";
?>
