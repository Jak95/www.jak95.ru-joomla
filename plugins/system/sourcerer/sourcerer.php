<?php
/**
 * Main Plugin File
 * Does all the magic!
 *
 * @package			Sourcerer
 * @version			2.11.3
 *
 * @author			Peter van Westen <peter@nonumber.nl>
 * @link			http://www.nonumber.nl
 * @copyright		Copyright © 2011 NoNumber! All Rights Reserved
 * @license			http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die();

// Import library dependencies
jimport( 'joomla.plugin.plugin' );

/**
 * Plugin that replaces Sourcerer code with its HTML / CSS / JavaScript / PHP equivalent
 */
class plgSystemSourcerer extends JPlugin
{
	function __construct( &$subject, $config )
	{
		$this->_pass = 0;
		parent::__construct( $subject, $config );
	}

	function onAfterRoute()
	{
		$this->_pass = 0;

		// return if disabled via url
		// return if current page is raw format
		// return if current page is a joomfishplus page
		if (
			JRequest::getCmd( 'disable_sourcerer' )
			|| JRequest::getCmd( 'format' ) == 'raw'
			|| JRequest::getCmd( 'option' ) == 'com_joomfishplus'
			|| JRequest::getInt( 'nn_qp' )
		) {
			return;
		}

		$app = JFactory::getApplication();

		// load the admin language file
		$lang = JFactory::getLanguage();
		if ( $lang->getTag() != 'en-GB' ) {
			// Loads English language file as fallback (for undefined stuff in other language file)
			$lang->load( 'plg_'.$this->_type.'_'.$this->_name, JPATH_ADMINISTRATOR, 'en-GB' );
		}
		$lang->load( 'plg_'.$this->_type.'_'.$this->_name, JPATH_ADMINISTRATOR, null, 1 );

		// return if NoNumber! Framework plugin is not installed
		jimport( 'joomla.filesystem.file' );
		if ( !JFile::exists( JPATH_PLUGINS.'/system/nnframework/nnframework.php' ) ) {
			if ( $app->isAdmin() && JRequest::getCmd( 'option' ) !== 'com_login' ) {
				$msg = JText::_( 'SRC_NONUMBER_FRAMEWORK_PLUGIN_NOT_INSTALLED' );
				$mq = $app->getMessageQueue();
				foreach ( $mq as $m ) {
					if ( $m['message'] == $msg ) {
						$msg = '';
						break;
					}
				}
				if ( $msg ) {
					$app->enqueueMessage( $msg, 'error' );
				}
			}
			return;
		}

		// return if current page is an administrator page (and not acymailing)
		if ( $app->isAdmin() && JRequest::getCmd( 'option' ) != 'com_acymailing' ) {
			return;
		}

		$this->_pass = 1;

		// load the admin language file
		$lang = JFactory::getLanguage();
		if ( $lang->getTag() != 'en-GB' ) {
			// Loads English language file as fallback (for undefined stuff in other language file)
			$lang->load( 'plg_'.$this->_type.'_'.$this->_name, JPATH_SITE, 'en-GB' );
		}
		$lang->load( 'plg_'.$this->_type.'_'.$this->_name, JPATH_SITE, null, 1 );

		// Load plugin parameters
		require_once JPATH_PLUGINS.'/system/nnframework/helpers/parameters.php';
		$parameters = NNParameters::getInstance();
		$params = $parameters->getParams( $this->params->toObject(), JPATH_PLUGINS.'/'.$this->_type.'/'.$this->_name.'/'.$this->_name.'.xml' );

		// Include the Helper
		require_once JPATH_PLUGINS.'/'.$this->_type.'/'.$this->_name.'/helper.php';
		$class = get_class( $this ).'Helper';
		$this->helper = new $class ( $params );
	}

	function onContentPrepare( $context, &$article, &$params )
	{
		if ( $this->_pass ) {
			$this->helper->onContentPrepare( $article, $params );
		}
	}

	function onAfterDispatch()
	{
		if ( $this->_pass ) {
			$this->helper->onAfterDispatch();
		}
	}

	function onAfterRender()
	{
		if ( $this->_pass ) {
			$this->helper->onAfterRender();
		}
	}
}