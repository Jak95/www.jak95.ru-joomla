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

require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'dbutil.php');

/**
 * A util class with some static helper methodes used in GCalendar.
 *
 * @author allon
 */
class GCalendarUtil{

	/**
	 * Loads JQuery if the component parameter is set to yes.
	 */
	public static function loadJQuery(){
		static $jQueryloaded;
		if($jQueryloaded == null){
			$param   = GCalendarUtil::getComponentParameter('loadJQuery');
			$document =& JFactory::getDocument();
			if(!JFactory::getApplication()->get('jquery', false) && ($param == 'yes' || empty($param))){
				$document->addScript(JURI::base().'components/com_gcalendar/libraries/jquery/jquery.min.js');
				JFactory::getApplication()->set('jquery', true);
			}
			$document->addScriptDeclaration("jQuery.noConflict();");
			$jQueryloaded = 'loaded';
		}
	}

	/**
	 * Returns the component parameter for the given key.
	 *
	 * @param $key
	 * @param $defaultValue
	 * @return the component parameter
	 */
	public static function getComponentParameter($key, $defaultValue = null){
		$params   = JComponentHelper::getParams('com_gcalendar');
		return $params->get($key, $defaultValue);
	}

	/**
	 * Returns the correct configured frontend language for the
	 * joomla web site.
	 * The format is something like de-DE which can be passed to google.
	 *
	 * @return the frontend language
	 */
	public static function getFrLanguage(){
		$conf	=& JFactory::getConfig();
		return $conf->getValue('config.language');
		//		$params   = JComponentHelper::getParams('com_languages');
		//		return $params->get('site', 'en-GB');
	}

	/**
	 * Returns a valid Item ID for the given calendar id. If none is found
	 * NULL is returned.
	 *
	 * @param $cal_id
	 * @return the item id
	 */
	public static function getItemId($cal_id){
		$component	= &JComponentHelper::getComponent('com_gcalendar');
		$menu = &JSite::getMenu();
		$items		= $menu->getItems('component_id', $component->id);

		if (is_array($items)){
			foreach($items as $item) {
				$paramsItem	=& $menu->getParams($item->id);
				$calendarids = $paramsItem->get('calendarids');
				if(empty($calendarids)){
					$results = GCalendarDBUtil::getAllCalendars();
					if($results){
						$calendarids = array();
						foreach ($results as $result) {
							$calendarids[] = $result->id;
						}
					}
				}
				$contains_gc_id = FALSE;
				if ($calendarids){
					if( is_array( $calendarids ) ) {
						$contains_gc_id = in_array($cal_id,$calendarids);
					} else {
						$contains_gc_id = $cal_id == $calendarids;
					}
				}
				if($contains_gc_id){
					return $item->id;
				}
			}
		}
		return null;
	}

	/**
	 * The simplepie event is rendered for the given formats and
	 * returned as HTML code.
	 *
	 * @param $event
	 * @param $format
	 * @param $dateformat
	 * @param $timeformat
	 * @return the HTML code of the efent
	 */
	public static function renderEvent(GCalendar_Entry $event, $format, $dateformat, $timeformat){
		$tz = GCalendarUtil::getComponentParameter('timezone');
		if($tz == ''){
			$tz = $event->getTimezone();
		}

		$itemID = GCalendarUtil::getItemId($event->getParam('gcid'));
		if(!empty($itemID)){
			$itemID = '&Itemid='.$itemID;
		}else{
			$menu=JSite::getMenu();
			$activemenu=$menu->getActive();
			if($activemenu != null)
			$itemID = '&Itemid='.$activemenu->id;
		}

		// These are the dates we'll display
		$startDate = GCalendarUtil::formatDate($dateformat, $event->getStartDate());
		$startTime = GCalendarUtil::formatDate($timeformat, $event->getStartDate());
		$endDate = GCalendarUtil::formatDate($dateformat, $event->getEndDate());
		$endTime = GCalendarUtil::formatDate($timeformat, $event->getEndDate());

		$temp_event = $format;

		switch($event->getDayType()){
			case GCalendar_Entry::SINGLE_WHOLE_DAY:
				$temp_event=str_replace("{startdate}",$startDate,$temp_event);
				$temp_event=str_replace("{starttime}","",$temp_event);
				$temp_event=str_replace("{dateseparator}","",$temp_event);
				$temp_event=str_replace("{enddate}","",$temp_event);
				$temp_event=str_replace("{endtime}","",$temp_event);
				break;
			case GCalendar_Entry::SINGLE_PART_DAY:
				$temp_event=str_replace("{startdate}",$startDate,$temp_event);
				$temp_event=str_replace("{starttime}",$startTime,$temp_event);
				$temp_event=str_replace("{dateseparator}","-",$temp_event);
				$temp_event=str_replace("{enddate}","",$temp_event);
				$temp_event=str_replace("{endtime}",$endTime,$temp_event);
				break;
			case GCalendar_Entry::MULTIPLE_WHOLE_DAY:
				$SECSINDAY=86400;
				$endDate = GCalendarUtil::formatDate($dateformat, $event->getEndDate()-$SECSINDAY);
				$temp_event=str_replace("{startdate}",$startDate,$temp_event);
				$temp_event=str_replace("{starttime}","",$temp_event);
				$temp_event=str_replace("{dateseparator}","-",$temp_event);
				$temp_event=str_replace("{enddate}",$endDate,$temp_event);
				$temp_event=str_replace("{endtime}","",$temp_event);
				break;
			case GCalendar_Entry::MULTIPLE_PART_DAY:
				$temp_event=str_replace("{startdate}",$startDate,$temp_event);
				$temp_event=str_replace("{starttime}",$startTime,$temp_event);
				$temp_event=str_replace("{dateseparator}","-",$temp_event);
				$temp_event=str_replace("{enddate}",$endDate,$temp_event);
				$temp_event=str_replace("{endtime}",$endTime,$temp_event);
				break;
		}
		if(GCalendarUtil::getComponentParameter('event_description_format', 1) == 2) {
			$desc = html_entity_decode($event->getContent());
		}else{
			//Make any URLs used in the description also clickable
			$desc = preg_replace("@(src|href)=\"https?\://@i",'\\1="',$event->getContent());
			$desc = preg_replace("@(((f|ht)tps?://)[^\"\'\>\s]+)@",'<a href="\\1" target="_blank">\\1</a>', $desc);
			//or "�(((f|ht)tp:\/\/)[\-a-zA-Z0-9@:%_\+\.~#\?,\/=&;]+)�"
		}

		$temp_event=str_replace("{title}",$event->getTitle(),$temp_event);
		$temp_event=str_replace("{description}",$desc,$temp_event);
		$temp_event=str_replace("{where}",$event->getLocation(),$temp_event);
		$temp_event=str_replace("{backlink}",htmlentities(JRoute::_('index.php?option=com_gcalendar&view=event&eventID='.$event->getGCalId().'&gcid='.$event->getParam('gcid').$itemID)),$temp_event);
		$temp_event=str_replace("{link}",$event->getLink().'&ctz='.$tz,$temp_event);
		$temp_event=str_replace("{maplink}","http://maps.google.com/?q=".urlencode($event->getLocation()),$temp_event);
		$temp_event=str_replace("{calendarname}",$event->getParam('gcname'),$temp_event);
		$temp_event=str_replace("{calendarcolor}",$event->getParam('gccolor'),$temp_event);
		// Accept and translate HTML
		$temp_event = html_entity_decode($temp_event);
		return $temp_event;
	}

	/**
	 * Returns the faded color for the given color.
	 *
	 * @param $color
	 * @param $percentage
	 * @return the faded color
	 */
	public static function getFadedColor($color, $percentage = 85) {
		$percentage = 100 - $percentage;
		$rgbValues = array_map( 'hexDec', str_split( ltrim($color, '#'), 2 ) );

		for ($i = 0, $len = count($rgbValues); $i < $len; $i++) {
			$rgbValues[$i] = decHex( floor($rgbValues[$i] + (255 - $rgbValues[$i]) * ($percentage / 100) ) );
		}

		return '#'.implode('', $rgbValues);
	}

	/**
	 * Translates day of week number to a string.
	 *
	 * @param	integer	The numeric day of the week.
	 * @param	boolean	Return the abreviated day string?
	 * @return	string	The day of the week.
	 */
	public static function dayToString($day, $abbr = false)
	{
		$name = '';
		switch ($day) {
			case 0:
				$name = $abbr ? JText::_('SUN') : JText::_('SUNDAY');
				break;
			case 1:
				$name = $abbr ? JText::_('MON') : JText::_('MONDAY');
				break;
			case 2:
				$name = $abbr ? JText::_('TUE') : JText::_('TUESDAY');
				break;
			case 3:
				$name = $abbr ? JText::_('WED') : JText::_('WEDNESDAY');
				break;
			case 4:
				$name = $abbr ? JText::_('THU') : JText::_('THURSDAY');
				break;
			case 5:
				$name = $abbr ? JText::_('FRI') : JText::_('FRIDAY');
				break;
			case 6:
				$name = $abbr ? JText::_('SAT') : JText::_('SATURDAY');
				break;
		}
		return addslashes($name);
	}

	/**
	 * Translates month number to a string.
	 *
	 * @param	integer	The numeric month of the year.
	 * @param	boolean	Return the abreviated month string?
	 * @return	string	The month of the year.
	 */
	public static function monthToString($month, $abbr = false)
	{
		$name = '';
		switch ($month) {
			case 1:
				$name = $abbr ? JText::_('JANUARY_SHORT')	: JText::_('JANUARY');
				break;
			case 2:
				$name = $abbr ? JText::_('FEBRUARY_SHORT')	: JText::_('FEBRUARY');
				break;
			case 3:
				$name = $abbr ? JText::_('MARCH_SHORT')		: JText::_('MARCH');
				break;
			case 4:
				$name = $abbr ? JText::_('APRIL_SHORT')		: JText::_('APRIL');
				break;
			case 5:
				$name = $abbr ? JText::_('MAY_SHORT')		: JText::_('MAY');
				break;
			case 6:
				$name = $abbr ? JText::_('JUNE_SHORT')		: JText::_('JUNE');
				break;
			case 7:
				$name = $abbr ? JText::_('JULY_SHORT')		: JText::_('JULY');
				break;
			case 8:
				$name = $abbr ? JText::_('AUGUST_SHORT')	: JText::_('AUGUST');
				break;
			case 9:
				$name = $abbr ? JText::_('SEPTEMBER_SHORT')	: JText::_('SEPTEMBER');
				break;
			case 10:
				$name = $abbr ? JText::_('OCTOBER_SHORT')	: JText::_('OCTOBER');
				break;
			case 11:
				$name = $abbr ? JText::_('NOVEMBER_SHORT')	: JText::_('NOVEMBER');
				break;
			case 12:
				$name = $abbr ? JText::_('DECEMBER_SHORT')	: JText::_('DECEMBER');
				break;
		}
		return addslashes($name);
	}

	public static function formatDate($dateFormat,$date,$strf = false){
		$dateObj = JFactory::getDate($date);

		$gcTz = GCalendarUtil::getComponentParameter('timezone');
		if(!empty($gcTz)){
			$tz = new DateTimeZone($gcTz);
			$dateObj->setTimezone($tz);
		}
		if ($strf) {
			return $dateObj->toFormat($dateFormat, true);
		}

		return $dateObj->format($dateFormat, true);
	}

	public static function getActions($calendarId = 0){
		$user  = JFactory::getUser();
		$result  = new JObject;

		if (empty($calendarId)) {
			$assetName = 'com_gcalendar';
		}
		else {
			$assetName = 'com_gcalendar.calendar.'.(int) $calendarId;
		}

		$actions = array('core.admin', 'core.manage', 'core.create', 'core.edit', 'core.delete');

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
?>