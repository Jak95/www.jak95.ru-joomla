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
$SECSINDAY=86400;
if(!empty($this->calendars)){
	foreach ($this->calendars as $calendar){
		$itemID = null;
		foreach ($calendar as $event) {
			if($itemID == null){
				$itemID = GCalendarUtil::getItemId($event->getParam('gcid'));
				$menus	= &JSite::getMenu();
				$params = $menus->getParams($itemID);
				if(empty($params))
				$params = new JParameter('');
				$dateformat = $params->get('description_date_format', 'd.m.Y');
				$timeformat = $params->get('description_time_format', 'H:i');
				$event_display = $params->get('description_format', '<p>{startdate} {starttime} {dateseparator} {enddate} {endtime}<br/>{description}</p>');

				if(!empty($itemID)) {
					$itemID = '&Itemid='.$itemID;
				} else {
					$menu=JSite::getMenu();
					$activemenu=$menu->getActive();
					if($activemenu != null)
					$itemID = '&Itemid='.$activemenu->id;
				}
			}
			$allDayEvent = $event->getDayType() == GCalendar_Entry::SINGLE_WHOLE_DAY || $event->getDayType() == GCalendar_Entry::MULTIPLE_WHOLE_DAY;
			$description = GCalendarUtil::renderEvent($event, $event_display, $dateformat, $timeformat);
			if(strlen($description) > 200)
			$description = substr($description, 0, 196).' ...';
			$data[] = array(
			'id' => $event->getId(),
			'title' => htmlspecialchars_decode($event->getTitle()),
			'start' => GCalendarUtil::formatDate('Y-m-d\TH:i:s', $event->getStartDate()),
			'end' => GCalendarUtil::formatDate('Y-m-d\TH:i:s',$allDayEvent? $event->getEndDate() - $SECSINDAY:$event->getEndDate()),
			'url' => JRoute::_('index.php?option=com_gcalendar&view=event&eventID='.$event->getGCalId().'&gcid='.$event->getParam('gcid').$itemID),
			'className' => "gcal-event_gccal_".$event->getParam('gcid'),
			'allDay' => $allDayEvent,
			'description' => $description
			);
		}
	}
}
echo json_encode($data);
?>