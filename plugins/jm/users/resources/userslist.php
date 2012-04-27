<?php
/**
 * @package	JM
 * @version 0.2
 * @author 	Rafael Corral
 * @link 	http://jommobile.com
 * @copyright Copyright (C) 2012 Rafael Corral. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class UsersJMResourceUsersList extends JMResource
{
	public function get()
	{
		$db = JFactory::getDBO();
		$_columns = '`id`, `name`';

		$where = '';
		if ( JRequest::getInt( 'access_level' ) ) {
			// Does not include registered users in the list
			$where = ' AND `gid` > 18';
		}

		$columns = JRequest::getString( 'columns' );
		$order   = JRequest::getCmd( 'order', 'name' );

		if ( !$columns ) {
			$columns = $_columns;
		}
		
		preg_match_all( '/`?\w+`?/im', $columns, $matches );

		if ( !empty( $matches[0] ) ) {
			$columns = '';
			foreach ( $matches[0] as $match ) {
				$columns .= "{$match}, ";
			}
			$columns = substr( $columns, 0, -2 );
		} else {
			$columns = $_columns;
		}

		$query = "SELECT {$columns}
			FROM #__users
				WHERE block = 0
				$where
					ORDER BY {$order} ASC";
		$db->setQuery( $query );
		$users = $db->loadObjectList( 'id' );

		$this->plugin->setResponse( $users );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}