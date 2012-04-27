<?php
/**
 * Main Plugin File
 * Does all the magic!
 *
 * @package			Better Preview
 * @version			1.11.3
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
 * Plugin that removes the core preview link
 */
class plgSystemBetterPreview extends JPlugin
{
	function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}

	function onAfterRoute()
	{
		$this->_pass = 0;
		$app = JFactory::getApplication();

		// if current page is not an administrator page, return nothing
		if ( $app->isSite() ) {
			return;
		}

		$document = JFactory::getDocument();
		$docType = $document->getType();

		// only in html
		if ( $docType != 'html' ) {
			return;
		}

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
				$msg = JText::_( 'BP_NONUMBER_FRAMEWORK_PLUGIN_NOT_INSTALLED' );
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

		$document->addStyleDeclaration( 'span.viewsite { display:none !important; }' );
	}
}