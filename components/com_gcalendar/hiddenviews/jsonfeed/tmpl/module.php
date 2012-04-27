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
$data = array();
$SECSINDAY = 86400;

$startDate = JRequest::getInt('start', null);
$endDate = JRequest::getInt('end', null);
$browserTz = JRequest::getInt('browserTimezone', null);
$moduleId = JRequest::getInt('moduleid', 0);
if(!empty($browserTz))
$browserTz = $browserTz * 60;
else
$browserTz = 0;

$serverTz = ini_get('date.timezone');
if(function_exists('date_default_timezone_get'))
$serverTz = date_default_timezone_get();

$requestedDayStart = $startDate - $browserTz - date('Z', $startDate);
$requestedDayEnd = $requestedDayStart + $SECSINDAY;
$wasDSTStart = GCalendarModelJSONFeed::isDST($requestedDayStart, $serverTz);
$wasDSTEnd = GCalendarModelJSONFeed::isDST($requestedDayEnd, $serverTz);

while ($requestedDayStart < $endDate) {
	$result = array();
	$linkIDs = '';
	$description = '';
	if(!empty($this->calendars)){
		foreach ($this->calendars as $calendar){
			$calID = null;
			if(empty($calendar)){
				continue;
			}
			foreach ($calendar as $item) {
				if($requestedDayStart  < $item->getEndDate()
				&& $item->getStartDate() < $requestedDayEnd){
					$result[] = $item;
					$calID = $item->getParam('gcid').',';
					$description .= '<li><font color="#'.$item->getParam('gccolor').'">'.htmlspecialchars_decode($item->getTitle()).'</font></li>';
				}
			}
			if($calID != null)
			$linkIDs .= $calID;
		}
	}
	if(!empty($result)){
		$linkIDs = trim($linkIDs, ",");
		$day = strftime('%d', $requestedDayStart);
		$month = strftime('%m', $requestedDayStart);
		$year = strftime('%Y', $requestedDayStart);
		$url = JRoute::_('index.php?option=com_gcalendar&view=day&gcids='.$linkIDs.'#year='.$year.'&month='.$month.'&day='.$day);

		$data[] = array(
			'id' => time(),
			'title' => utf8_encode(chr(160)), //space only works in IE, empty only in Chrome... sighh 
			'start' => strftime('%Y-%m-%dT%H:%M:%S', $requestedDayStart),
			'url' => $url,
			'allDay' => true,
		//			'end' => $requestedDayEnd - 10,
			'className' => "gcal-module_event_gccal_".$moduleId,
			'description' => sprintf(JText::_('COM_GCALENDAR_JSON_VIEW_EVENT_TITLE'), count($result)).'<ul>'.$description.'</ul>'
		);
	}

	$requestedDayStart += $SECSINDAY;
	$isDST = GCalendarModelJSONFeed::isDST($requestedDayStart, $serverTz);
	$dstAdjustment = 0;
	if($wasDSTStart && !$isDST){
		$dstAdjustment = 3600;
		$wasDSTStart = $isDST;
	} else if(!$wasDSTStart && $isDST){
		$dstAdjustment = -3600;
		$wasDSTStart = $isDST;
	}
	$requestedDayStart += $dstAdjustment;

	$requestedDayEnd = $requestedDayStart + $SECSINDAY;
	$isDST = GCalendarModelJSONFeed::isDST($requestedDayEnd, $serverTz);
	$dstAdjustment = 0;
	if($wasDSTEnd && !$isDST){
		$dstAdjustment = 3600;
		$wasDSTEnd = $isDST;
	} else if(!$wasDSTEnd && $isDST){
		$dstAdjustment = -3600;
		$wasDSTEnd = $isDST;
	}
	$requestedDayEnd += $dstAdjustment;
}
echo json_encode($data);
?>
