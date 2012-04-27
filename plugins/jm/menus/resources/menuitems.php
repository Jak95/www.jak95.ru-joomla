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

class MenusJMResourceMenuItems extends JMResource
{
	public function get()
	{
		JMHelper::setSessionUser();

		require_once JPATH_ADMINISTRATOR.'/components/com_menus/models/items.php';
		require_once JPATH_PLUGINS.'/jm/menus/resources/helper.php';

		$model = JModel::getInstance('JMHelperModel', 'MenuItemsModel');
		$model->_setCache('getstart', $model->getState('list.start'));
		$menuitems = $model->getItems();

		if ( false === $menuitems || ( empty( $menuitems ) && $model->getError() ) ) {
			$response = $this->getErrorResponse( 400, $model->getError() );
		} else {
			$response = $menuitems;
		}

		$this->plugin->setResponse( $response );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}

	public function delete( $id = null )
	{
		// Include dependencies
		jimport('joomla.application.component.controller');
		jimport('joomla.form.form');
		jimport('joomla.database.table');

		require_once JPATH_ADMINISTRATOR . '/components/com_menus/controllers/items.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_menus/models/item.php';
		JForm::addFormPath( JPATH_ADMINISTRATOR . '/components/com_menus/models/forms/' );

		// Fake parameters
		$_POST['task'] = 'trash';
		$_REQUEST['task'] = 'trash';
		$_REQUEST[JUtility::getToken()] = 1;
		$_POST[JUtility::getToken()] = 1;

		JFactory::getLanguage()->load('com_menus', JPATH_ADMINISTRATOR);
		$controller = new MenusControllerItems();
		try {
			$controller->execute('trash');
		} catch ( JException $e ) {
			$success = false;
			$controller->set('messageType', 'error');
			$controller->set('message', $e->getMessage() );
		}

		if ( $controller->getError() ) {
			$response = $this->getErrorResponse( 400, $controller->getError() );
		} elseif ( 'error' == $controller->get('messageType') ) {
			$response = $this->getErrorResponse( 400, $controller->get('message') );
		} else {
			$response = $this->getSuccessResponse( 200, $controller->get('message') );
		}

		$this->plugin->setResponse( $response );
	}
}