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

$dispatcher = JDispatcher::getInstance();
JPluginHelper::importPlugin('gcalendar');

$event = $this->event;

$itemID = GCalendarUtil::getItemId(JRequest::getVar('gcid', null));
if(!empty($itemID) && JRequest::getVar('tmpl', null) != 'component' && $event != null){
	$component	= &JComponentHelper::getComponent('com_gcalendar');
	$menu = &JSite::getMenu();
	$item = $menu->getItem($itemID);
	if($item !=null){
		$backLinkView = $item->query['view'];
		$dateHash = '';
		if($backLinkView == 'gcalendar'){
			$day = strftime('%d', $event->getStartDate());
			$month = strftime('%m', $event->getStartDate());
			$year = strftime('%Y', $event->getStartDate());
			$dateHash = '#year='.$year.'&month='.$month.'&day='.$day;
		}

		$document = &JFactory::getDocument();
		$document->addStyleSheet(JURI::base(). 'components/com_gcalendar/views/gcalendar/tmpl/gcalendar.css' );

		echo "<table class=\"gcalendar-table\"><tr><td valign=\"middle\">\n";
		echo '<a href="'.JRoute::_('index.php?option=com_gcalendar&Itemid='.$itemID.$dateHash)."\">\n";
		echo "<img id=\"prevBtn_img\" height=\"16\" border=\"0\" width=\"16\" alt=\"backlink\" src=\"media/com_gcalendar/images/back.png\"/>\n";
		echo "</a></td><td valign=\"middle\">\n";
		echo '<a href="'.JRoute::_('index.php?option=com_gcalendar&Itemid='.$itemID.$dateHash).'">'.JText::_( 'COM_GCALENDAR_EVENT_VIEW_CALENDAR_BACK_LINK' )."</a>\n";
		echo "</td></tr></table>\n";
	}
}
if($event == null){
	echo "no event found";
}else{
	// the date formats from http://php.net/strftime
	$dateformat = GCalendarUtil::getComponentParameter('event_date_format', 'd.m.Y');
	$timeformat = GCalendarUtil::getComponentParameter('event_time_format', 'H:i');

	// These are the dates we'll display
	$startDate = GCalendarUtil::formatDate($dateformat, $event->getStartDate());
	$startTime = GCalendarUtil::formatDate($timeformat, $event->getStartDate());
	$endDate = GCalendarUtil::formatDate($dateformat, $event->getEndDate());
	$endTime = GCalendarUtil::formatDate($timeformat, $event->getEndDate());
	$dateSeparator = '-';

	$timeString = $startTime.' '.$startDate.' '.$dateSeparator.' '.$endTime.' '.$endDate;
	$copyDateTimeFormat = 'Ymd';
	switch($event->getDayType()){
		case GCalendar_Entry::SINGLE_WHOLE_DAY:
			$timeString = $startDate;
			$copyDateTimeFormat = 'Ymd';
			break;
		case GCalendar_Entry::SINGLE_PART_DAY:
			$timeString = $startDate.' '.$startTime.' '.$dateSeparator.' '.$endTime;
			$copyDateTimeFormat = 'Ymd\THis';
			break;
		case GCalendar_Entry::MULTIPLE_WHOLE_DAY:
			$SECSINDAY=86400;
			$endDate = GCalendarUtil::formatDate($dateformat, $event->getEndDate()-$SECSINDAY);
			$timeString = $startDate.' '.$dateSeparator.' '.$endDate;
			$copyDateTimeFormat = 'Ymd';
			break;
		case GCalendar_Entry::MULTIPLE_PART_DAY:
			$timeString = $startTime.' '.$startDate.' '.$dateSeparator.' '.$endTime.' '.$endDate;
			$copyDateTimeFormat = 'Ymd\THis';
			break;
	}

	$document =& JFactory::getDocument();
	$document->addStyleSheet(JURI::base().'components/com_gcalendar/hiddenviews/event/tmpl/default.css');

	echo "<div class=\"event_content\"><table id=\"content_table\">\n";

	echo "<tr><td colspan=\"2\">\n";
	$dispatcher->trigger('onGCEventLoadedBefore', array($event));
	echo "</td></tr>\n";

	if(GCalendarUtil::getComponentParameter('show_calendar_name', 1) == 1){
		echo "<tr><td class=\"event_content_key\">".JText::_( 'COM_GCALENDAR_EVENT_VIEW_CALENDAR_NAME' ).": </td><td>".$event->getParam('gcname')."</td></tr>\n";
	}
	if(GCalendarUtil::getComponentParameter('show_event_title', 1) == 1){
		echo "<tr><td class=\"event_content_key\">".JText::_( 'COM_GCALENDAR_EVENT_VIEW_EVENT_TITLE' ).": </td><td>".$event->getTitle()."</td></tr>\n";
	}
	if(GCalendarUtil::getComponentParameter('show_event_date', 1) == 1){
		echo "<tr><td class=\"event_content_key\">".JText::_( 'COM_GCALENDAR_EVENT_VIEW_WHEN' ).": </td><td>".$timeString."</td></tr>\n";
	}
	if(GCalendarUtil::getComponentParameter('show_event_attendees', 2) == 1){
		$attendeesString = '';
		foreach ($event->getWho() as $a) {
			$attendeesString .= $a->getValueString()." <a href=\"javascript:sdafgkl437jeeee('".base64_encode(str_replace('@','#',$a->getEmail()))."')\"><img height=\"11\" border=\"0\" width=\"16\" alt=\"email\" src=\"media/com_gcalendar/images/mail.png\"/></a>,";
		}
		$attendeesString = rtrim($attendeesString, ',');
		echo "<tr><td class=\"event_content_key\">".JText::_( 'COM_GCALENDAR_EVENT_VIEW_ATTENDEES' ).": </td><td style=\"valign:top\">".$attendeesString."</td></tr>\n";
	}
	if(GCalendarUtil::getComponentParameter('show_event_location', 1) == 1){
		$loc = $event->getLocation();
		if(!empty($loc)){
			echo "<tr><td class=\"event_content_key\">".JText::_( 'COM_GCALENDAR_EVENT_VIEW_LOCATION' ).": </td><td>".$loc."</td></tr>\n";
			if(GCalendarUtil::getComponentParameter('show_event_location_map', 1) == 1){
				echo "<tr><td colspan=\"2\"><iframe width=\"100%\" height=\"300px\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" src=\"http://maps.google.com/maps?q=".urlencode($loc)."&hl=".substr(GCalendarUtil::getFrLanguage(),0,2)."&output=embed\"></iframe></td></tr>\n";
			}
		}
	}
	$desc = preg_replace("@(src|href)=\"https?://@i",'\\1="',$event->getContent());
	if(GCalendarUtil::getComponentParameter('show_event_description', 1) == 1 && !empty($desc)) {
		if(GCalendarUtil::getComponentParameter('event_description_format', 1) == 2) {
			echo html_entity_decode($event->getContent());
		}else{
			echo "<tr><td class=\"event_content_key\">".JText::_( 'COM_GCALENDAR_EVENT_VIEW_DESCRIPTION' ).": </td><td>".
			htmlspecialchars_decode(nl2br(preg_replace("@(((f|ht)tp:\/\/)[^\"\'\>\s]+)@",'<a href="\\1" target="_blank">\\1</a>', $desc)))."</td></tr>\n";
		}
	}
	if(GCalendarUtil::getComponentParameter('show_event_author', 2) == 1){
		foreach ($event->getAuthor() as $author) {
			$document->addScript(JURI::base().'components/com_gcalendar/hiddenviews/event/tmpl/default.js');
			echo "<tr><td class=\"event_content_key\">".JText::_( 'COM_GCALENDAR_EVENT_VIEW_AUTHOR' ).": </td><td style=\"valign:top\">".$author->getName()." <a href=\"javascript:sdafgkl437jeeee('".base64_encode(str_replace('@','#',$author->getEmail()))."')\"><img height=\"11\" border=\"0\" width=\"16\" alt=\"email\" src=\"media/com_gcalendar/images/mail.png\"/></a></td></tr>\n";
		}
	}

	if(GCalendarUtil::getComponentParameter('show_event_copy_info', 1) == 1){
		$urlText = 'action=TEMPLATE&amp;text='.urlencode($event->getTitle());
		$urlText .= '&amp;dates='.GCalendarUtil::formatDate($copyDateTimeFormat, $event->getStartDate()).'%2F'.GCalendarUtil::formatDate($copyDateTimeFormat, $event->getEndDate());
		$urlText .= '&amp;location='.urlencode($event->getLocation());
		$urlText .= '&amp;details='.urlencode($event->getContent());
		$urlText .= '&amp;hl='.GCalendarUtil::getFrLanguage().'&amp;ctz='.GCalendarUtil::getComponentParameter('timezone');
		$urlText .= '&amp;sf=true&amp;output=xml';
		echo "<tr><td class=\"event_content_key\">".JText::_( 'COM_GCALENDAR_EVENT_VIEW_COPY' ).": </td><td><a target=\"_blank\" href=\"http://www.google.com/calendar/render?".$urlText."\">".JText::_( 'COM_GCALENDAR_EVENT_VIEW_COPY_TO_MY_CALENDAR' )."</a></td></tr>\n";

		/** Modified Code for ical file */
		$ical_timeString_start =  $startTime.' '.$startDate;
		$ical_timeString_start = strtotime($ical_timeString_start);
		$ical_timeString_end =  $endTime.' '.$endDate;
		$ical_timeString_end = strtotime($ical_timeString_end);
		$loc = $event->getLocation();
		echo "<tr><td class=\"event_content_key\"></td><td><a href=\"".JRoute::_("index.php?option=com_gcalendar&view=ical&format=raw&start=".$ical_timeString_start."&end=".$ical_timeString_end."&title=".$event->getTitle()."&location=".$loc)."\">".JText::_('COM_GCALENDAR_EVENT_VIEW_COPY_TO_MY_CALENDAR_ICS')."</a></td></tr>";
		/** End Modified Code */
	}
	echo "<tr><td colspan=\"2\">\n";
	$dispatcher->trigger('onGCEventLoadedAfter', array($event));
	echo "</td></tr>\n";

	echo "</table></div>\n";
}
echo "<div style=\"text-align:center;margin-top:10px\" id=\"gcalendar_powered\"><a href=\"http://g4j.laoneo.net\">Powered by GCalendar</a></div>\n";
?>