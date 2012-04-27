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

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class GCalendarModelJSONFeed extends JModel {

	/**
	 * Returns a Simplepie feed for the calendar according
	 * to the parameter gcid.
	 * If there is a menu available for this calendar the
	 * cache is used and configured from the menu parameters.
	 */
	function getGoogleCalendarFeeds() {
		$startDate = JRequest::getVar('start', null);
		$endDate = JRequest::getVar('end', null);

		$browserTz = JRequest::getInt('browserTimezone', null);
		if(!empty($browserTz))
		$browserTz = $browserTz * 60;
		else
		$browserTz = 0;

		$serverOffset = date('Z', $startDate);
		$startDate = $startDate - $browserTz - $serverOffset - GCalendarModelJSONFeed::getGCalendarTZOffset($startDate);
		$endDate = $endDate - $browserTz - $serverOffset - GCalendarModelJSONFeed::getGCalendarTZOffset($endDate);

		$calendarids = '';
		if(JRequest::getVar('gcids', null) != null){
			if(is_array(JRequest::getVar('gcids', null)))
			$calendarids = JRequest::getVar('gcids', null);
			else
			$calendarids = explode(',', JRequest::getVar('gcids', null));
		}else{
			$calendarids = JRequest::getVar('gcid', null);
		}
		$results = GCalendarDBUtil::getCalendars($calendarids);
		if(empty($results))
		return null;

		$calendars = array();
		foreach ($results as $result) {
			if(empty($result->calendar_id))
			continue;
				
			$events = GCalendarZendHelper::getEvents($result, $startDate, $endDate, 1000);
			if($events == null){
				continue;
			}
			$calendars[] = $events;
		}

		return $calendars;
	}

	/**
	 * Returns the GCalendar timezone offset in seconds. The given
	 * date is used to be DST compatible.
	 *
	 * @param $date
	 * @return offset in seconds
	 */
	function getGCalendarTZOffset($date = null) {
		static $tzs;
		if($tzs == null){
			$tzs = parse_ini_file(JPATH_SITE.DS.'components'.DS.'com_gcalendar'.DS.'models'.DS.'timezones.ini');
		}
		$tz = GCalendarUtil::getComponentParameter('timezone');
		$offset = '00:00';
		if(!empty($tz)){
			$offset = $tzs[$tz];
		}
		if($date == null) $date = time();

		$dst = GCalendarModelJSONFeed::isDST($date) ? 1 : 0;
		$gcalendarOffset = (((int)substr($offset, 1, 3) - $dst)*60)+substr($offset,3);
		$gcalendarOffset = substr($offset, 0, 1) == '-' ? -1 * $gcalendarOffset : $gcalendarOffset;
		return $gcalendarOffset * 60;
	}

	/**
	 * Checks if the DST applyes to the timezone of GCalendar for the given date.
	 *
	 * @param $date unix timestamp
	 * @param $tz the timezone
	 * @return if is DST
	 */
	function isDST($date, $tz = null){
		if(empty($tz))
		$tz = GCalendarUtil::getComponentParameter('timezone');
		if(class_exists('DateTimeZone') && !empty($tz)){
			$gtz = new DateTimeZone($tz);
			return $gtz->getOffset(new DateTime('2007-01-01 01:00')) != $gtz->getOffset(new DateTime(strftime('%Y-%m-%d %H:%M', $date))) ? true : false;
		}
		return false;
	}

}
