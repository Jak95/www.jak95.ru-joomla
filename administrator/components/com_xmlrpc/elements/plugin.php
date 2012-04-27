<?php
/**
 * @version		$Id: Plugin.php 12345 2011-01-28 00:00:00Z kozaki $
 * @package		XMLRPC
 * @subpackage	Parameter
 * @copyright		Copyright (C) 2007 - 2011 Joomler!.net. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */

// No direct access.
defined('JPATH_BASE') or die;

/**
 * Renders a filelist element
 *
 * @package		Joomla.Framework
 * @subpackage	Parameter
 * @since		1.5
 */

class JFormFieldPlugin extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $type = 'Plugin';

	protected function getInput()
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);
		$query->select('element AS value, element AS text');
		$query->from('`#__extensions`');
		$query->where('`type` = '. $db->quote('plugin'));
		$query->where('`folder`= '. $db->quote('xmlrpc'));
		$query->where('`access` IN ('. implode(',', $user->getAuthorisedViewLevels()). ')');
		$query->where('`enabled` = 1');
		$query->order('`extension_id` DESC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if(count($rows) < 1){
			return JText::_('COM_XMLRPC_WARNING_NOTHAVE_PLUGIN');
		}

		return JHtml::_('select.genericlist', $rows, $this->getName($this->fieldname),
			array(
				'id' => $this->id,
				'list.attr' => 'class="inputbox" size="1"',
				'list.select' => $this->value
			)
		);
	}
}