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
 * @copyright 2007-2012 Allon Moritz
 * @since 2.5.0
 */

class GCalendarZendHelper{

	const SORT_ORDER_ASC = 'ascending';
	const SORT_ORDER_DESC = 'descending';

	const ORDER_BY_START_TIME = 'starttime';
	const ORDER_BY_LAST_MODIFIED = 'lastmodified';

	public static function getEvents($calendar, $startDate = null, $endDate = null, $max = 1000, $filter = null, $orderBy = GCalendarZendHelper::ORDER_BY_START_TIME, $pastEvents = false, $sortOrder = GCalendarZendHelper::SORT_ORDER_ASC){
		$cache = & JFactory::getCache('com_gcalendar');
		$cache->setCaching(GCalendarUtil::getComponentParameter('gc_cache', 1) == '1');
		if(GCalendarUtil::getComponentParameter('gc_cache', 1) == 2){
			$conf =& JFactory::getConfig();
			$cache->setCaching($conf->getValue( 'config.caching' ));
		}
		$cache->setLifeTime(GCalendarUtil::getComponentParameter('gc_cache_time', 900));

		return $cache->call( array( 'GCalendarZendHelper', 'internalGetEvents' ), $calendar, $startDate, $endDate, $max, $filter, $orderBy, $pastEvents, $sortOrder);
	}

	public static function getEvent($calendar, $eventId){
		$cache = & JFactory::getCache('com_gcalendar');
		$cache->setCaching(GCalendarUtil::getComponentParameter('gc_cache', 1) == '1');
		if(GCalendarUtil::getComponentParameter('gc_cache', 1) == 2){
			$conf =& JFactory::getConfig();
			$cache->setCaching($conf->getValue( 'config.caching' ));
		}
		$cache->setLifeTime(GCalendarUtil::getComponentParameter('gc_cache_time', 900));

		return $cache->call( array( 'GCalendarZendHelper', 'internalGetEvent' ), $calendar, $eventId);
	}

	public static function internalGetEvents($calendar, $startDate = null, $endDate = null, $max = 1000, $filter = null, $orderBy = GCalendarZendHelper::ORDER_BY_START_TIME, $pastEvents = false, $sortOrder = GCalendarZendHelper::SORT_ORDER_ASC){
		$client = new Zend_Http_Client();
		$service = new Zend_Gdata_Calendar($client);

		$query = $service->newEventQuery();
		$query->setUser($calendar->calendar_id);
		if($calendar->magic_cookie != null){
			$query->setVisibility('private-'.$calendar->magic_cookie);
		}
		$query->setProjection('full');
		$query->setOrderBy($orderBy);
		$query->setSortOrder($sortOrder);
		$query->setSingleEvents('true');
		if(!empty($filter)){
			$query->setQuery($filter);
		}
		if($startDate != null){
			$query->setStartMin(strftime('%Y-%m-%dT%H:%M:%S', $startDate));
		}
		if($endDate != null){
			$query->setStartMax(strftime('%Y-%m-%dT%H:%M:%S',$endDate));
		}
		if($startDate == null && $endDate == null){
			$query->setFutureEvents($pastEvents ? 'false': 'true');
		}

		$query->setMaxResults($max);
		$timezone = GCalendarUtil::getComponentParameter('timezone');
		if(!empty($timezone)){
			$query->setParam('ctz', $timezone);
		}
		$query->setParam('hl', GCalendarUtil::getFrLanguage());

		try {
			$feed = $service->getFeed($query, 'GCalendar_Feed');
			foreach ($feed as $event) {
				$event->setParam('gcid', $calendar->id);
				$event->setParam('gccolor', $calendar->color);
				$event->setParam('gcname', $calendar->name);
				$event->setTimezone($feed->getTimezone());
			}
			return $feed;
		} catch (Zend_Gdata_App_Exception $e) {
			JError::raiseWarning(200, $e->getMessage());
			return null;
		}
	}

	public static function internalGetEvent($calendar, $eventId){
		$client = new Zend_Http_Client();
		$service = new Zend_Gdata_Calendar($client);

		$query = $service->newEventQuery();
		$query->setUser($calendar->calendar_id);
		if($calendar->magic_cookie != null){
			$query->setVisibility('private-'.$calendar->magic_cookie);
		}
		$query->setProjection('full');
		$query->setEvent($eventId);

		$timezone = GCalendarUtil::getComponentParameter('timezone');
		if(!empty($timezone)){
			$query->setParam('ctz', $timezone);
		}
		$query->setParam('hl', GCalendarUtil::getFrLanguage());

		try {
			$event = $service->getEntry($query, 'GCalendar_Entry');
			$event->setParam('gcid', $calendar->id);
			$event->setParam('gccolor', $calendar->color);
			$event->setParam('gcname', $calendar->name);
			if(!empty($timezone)){
				$event->setTimezone(new Zend_Gdata_Calendar_Extension_Timezone($timezone));
			}
			return $event;
		} catch (Zend_Gdata_App_Exception $e) {
			JError::raiseWarning(200, $e->getMessage());
			return null;
		}
	}

	public static function loadZendClasses() {
		static $zendLoaded;
		if($zendLoaded == null){
			ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . JPATH_ADMINISTRATOR . DS . 'components'. DS .'com_gcalendar' . DS . 'libraries');
			if(!class_exists('Zend_Loader')){
				require_once 'Zend/Loader.php';
			}
				
			Zend_Loader::loadClass('Zend_Gdata_AuthSub');
			Zend_Loader::loadClass('Zend_Gdata_HttpClient');
			Zend_Loader::loadClass('Zend_Gdata_Calendar');
			Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
			Zend_Loader::loadClass('GCalendar_Feed');
			Zend_Loader::loadClass('GCalendar_Entry');
			$zendLoaded = true;
		}
	}
}
GCalendarZendHelper::loadZendClasses();
?>