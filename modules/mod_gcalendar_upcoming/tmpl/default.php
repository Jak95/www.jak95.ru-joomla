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

defined( '_JEXEC' ) or die( 'Restricted access' );

$document = &JFactory::getDocument();
$document->addStyleSheet(JURI::base().'modules/mod_gcalendar_upcoming/tmpl/default.css');

$event_display = $params->get('output', '');
$group_format = trim($params->get('output_grouping', '')); // 12 Nov 2011 Grouping mod by Bernie Sumption

$dateformat = $params->get('date_format', 'd.m.Y');
$timeformat = $params->get('time_format', 'H:i');

echo $params->get( 'text_before' );
if(!empty($gcalendar_data)){
	if($params->get('images', 'no') != 'no') {
		echo '<p style="clear: both;"/>';
	}
	$lastHeading = ''; // 12 Nov 2011 Grouping mod by Bernie Sumption
	foreach( $gcalendar_data as $item){
		// 12 Nov 2011 Grouping mod by Bernie Sumption
		$groupHeading = GCalendarUtil::formatDate($group_format, $item->getStartDate());
		if ($groupHeading != $lastHeading) {
			$lastHeading = $groupHeading;
			echo str_replace("{header}", $groupHeading, $params->get('output_grouping_content', '<p style="clear: both;"><strong>{header}</strong></p>'));
		}
		// End mod
		
		// APRIL 2011 MOD - CALENDAR IMAGES by Tyson Moore
		if($params->get('images', 'no') != 'no') {
			$month_text = strtoupper(GCalendarUtil::formatDate('M', $item->getStartDate()));
			$day = GCalendarUtil::formatDate('d', $item->getStartDate());
			$colorImageBackground = $params->get('images', 'yes') == 'custom' ? '#'.$params->get('calimage_background') : GCalendarUtil::getFadedColor($item->getParam('gccolor'), 80);
			$colorMonth = $params->get('images', 'yes') == 'custom' ? $params->get('calimage_month') : 'FFFFFF';
			$colorDay = $params->get('images', 'yes') == 'custom' ? $params->get('calimage_day') : $item->getParam('gccolor');
			echo '<div class="gc_up_mod_img">';
			echo '<div class="gc_up_mod_month_background" style="background-color: ' . $colorImageBackground . ';"></div>';
			echo '<div class="gc_up_mod_month_text" style="color: #' . $colorMonth . ';">' . $month_text . '</div>';
			echo '<div class="gc_up_mod_day" style="color: #' . $colorDay . ';">' . $day . '</div>';
			echo '</div>';
		}
		//END MOD
		echo GCalendarUtil::renderEvent($item, $event_display, $dateformat, $timeformat);
		if($params->get('images', 'no') != 'no') {
			echo '<p style="clear: both;"/>';
		}
	}
}
echo $params->get( 'text_after' );
?>
