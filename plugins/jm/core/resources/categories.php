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

class CoreJMResourceCategories extends JMResource
{
	/**
	 * HTML list ready response
	 */
	public function get()
	{
		$extension  = JRequest::getWord( 'extension' );
		$categories = JHtml::_( 'category.options', $extension );

		// Verify permissions.  If the action attribute is set, then we scan the options.
		$action	= 'core.edit.own';

		// Get the current user object.
		$user = JFactory::getUser( JMHelper::getJMUserId() );

		foreach( $categories as $i => $cat ) {
			// To take save or create in a category you need to have create rights for that category
			// unless the item is already in that category.
			// Unset the option if the user isn't authorised for it. In this field assets are always categories.
			if ( $user->authorise( 'core.create', $extension . '.category.' . $cat->value ) != true
			) {
				unset( $categories[$i] );
			}
		}

		$this->plugin->setResponse( $categories );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}