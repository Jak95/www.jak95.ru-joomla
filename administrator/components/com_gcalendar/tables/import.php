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

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * GCalendar Table class
 *
 */
class TableImport extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	/**
	 * @var string
	 */
	var $name = null;
	
	/**
	 * @var string
	 */
	var $calendar_id = null;
	
	/**
	 * @var string
	 */
	var $magic_cookie = null;
	
	/**
	 * @var string
	 */
	var $color = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableImport(& $db) {
		parent::__construct('#__gcalendar', 'id', $db);
	}
}
?>
