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
jimport('joomla.database.table');

class GCalendarTableGCalendar extends JTable
{
	function __construct(&$db) 
	{
		parent::__construct('#__gcalendar', 'id', $db);
	}

	public function bind($array, $ignore = '') 
	{
		if (isset($array['params']) && is_array($array['params'])) 
		{
			// Convert the params field to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}
		return parent::bind($array, $ignore);
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_gcalendar.calendar.'.(int) $this->$k;
	}

	protected function _getAssetTitle()
	{
		return $this->name;
	}

	protected function _getAssetParentId()
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_gcalendar');
		return $asset->id;
	}
}
?>