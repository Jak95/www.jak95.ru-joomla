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

class ContentJMResourceArticle extends JMResource
{
	public function get()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_content/models/article.php';
		$model = JModel::getInstance( 'article', 'contentModel' );

		$this->plugin->setResponse( $model->getItem( JRequest::getInt( 'id', 0 ) )->getProperties() );
	}

	public function post()
	{
		// Set variables to be used
		JMHelper::setSessionUser();

		// Include dependencies
		jimport('joomla.application.component.controller');
		jimport('joomla.form.form');
		jimport('joomla.database.table');

		require_once JPATH_ADMINISTRATOR . '/components/com_content/controllers/article.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_content/models/article.php';
		JForm::addFormPath( JPATH_ADMINISTRATOR . '/components/com_content/models/forms/' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_content/tables/' );

		// Fake parameters
		$_POST['task'] = 'apply';
		$_REQUEST['task'] = 'apply';
		$_REQUEST[JUtility::getToken()] = 1;
		$_POST[JUtility::getToken()] = 1;

		$app = JFactory::getApplication();
		$context = 'com_content.edit.article';

		// Save article
		$controller = new ContentControllerArticle();
		$success = $controller->execute('apply');

		if ( $controller->getError() ) {
			$response = $this->getErrorResponse( 400, $controller->getError() );
		} elseif ( !$success ) {
			$response = $this->getErrorResponse( 400, JText::_('COM_JM_ERROR_OCURRED') );
		} else {
			$response = $this->getSuccessResponse( 201, $controller->get('message') );
			// Kind of a weird way of doing this, there has to be a better way?
			$values	= (array) $app->getUserState($context.'.id');
			$response->id = array_pop( $values );
			$app->setUserState($context.'.id', $values);
			// Checkin article
			$controller->getModel()->checkin( $response->id );
		}

		// Clear userstate for future requests
		$app->setUserState($context.'.id', array());

		$this->plugin->setResponse( $response );
	}

	public function put()
	{	
		$app = JFactory::getApplication();
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$context = 'com_content.edit.article';

		// Fake parameters
		$values	= (array) $app->getUserState($context.'.id');
		array_push($values, (int) $data['id']);
		$values = array_unique($values);
		$app->setUserState($context.'.id', $values);
		if ( !JRequest::getInt( 'id' ) ) {
			$_POST['id'] = $data['id'];
			$_REQUEST['id'] = $data['id'];
		}

		// Simply call post as Joomla will just save an article with an id
		$this->post();

		$response = $this->plugin->get( 'response' );
		if ( isset( $response->success ) && $response->success ) {
			JResponse::setHeader( 'status', 200, true );
			$response->code = 200;
			$this->plugin->setResponse( $response );
		}
	}
}