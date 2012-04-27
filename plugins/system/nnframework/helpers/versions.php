<?php
/**
 * NoNumber! Framework Helper File: VersionCheck
 *
 * @package			NoNumber! Framework
 * @version			12.1.6
 *
 * @author			Peter van Westen <peter@nonumber.nl>
 * @link			http://www.nonumber.nl
 * @copyright		Copyright © 2011 NoNumber! All Rights Reserved
 * @license			http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die();

class NNVersions
{
	public static $instance = null;

	public static function getInstance()
	{
		if ( !self::$instance ) {
			self::$instance = new NoNumberVersions;
		}

		return self::$instance;
	}

	public static function instance()
	{
		// backward compatibility
		return self::getInstance();
	}
}

class NoNumberVersions
{
	var $_version = '12.1.6';

	function getMessage( $extension = '', $xml = '', $version = '', $addmargin = 0 )
	{
		if ( !$extension || ( !$xml && !$version ) ) {
			return;
		}

		$alias = preg_replace( '#[^a-z\-]#', '', str_replace( '?', '-', strtolower( $extension ) ) );

		if ( $xml ) {
			$xml = JApplicationHelper::parseXMLInstallFile( JPATH_SITE.'/'.$xml );
			if ( $xml && isset( $xml['version'] ) ) {
				$version = $xml['version'];
			}
		}

		if ( !$version ) {
			return;
		}

		JHtml::_( 'behavior.mootools' );
		$document = JFactory::getDocument();
		$document->addScript( JURI::root( true ).'/plugins/system/nnframework/js/script.js?v='.$this->_version );
		$url = 'http://www.nonumber.nl/ext/version.php?ext='.$alias.'&version='.$version;
		$script = "
			window.addEvent( 'domready', function() {
				nnScripts.loadajax(
					'".$url."',
					'nnScripts.displayVersion( \'".$alias."\', data )',
					'nnScripts.displayVersion( \'".$alias."\', \'\' )'
				);
			});
		";
		$document->addScriptDeclaration( $script );

		$msg = html_entity_decode( JText::sprintf( 'NN_A_NEWER_VERSION_IS_AVAILABLE', 'http://www.nonumber.nl/'.$alias.'/download', '<span id="nonumber_newversionnumber_'.$alias.'"></span>', $version ), ENT_COMPAT, 'UTF-8' );

		$margin = $addmargin ? '10px;' : '3px;';
		$msg = '<div style="border:3px solid #F0DC7E;color:#CC0000;margin-bottom:'.$margin.'"><div style="padding: 2px 5px;background-color:#EFE7B8">'.$msg.'</div></div>';
		$msg = '<div id="nonumber_version_'.$alias.'" style="display: none;">'.$msg.'</div>';

		return $msg;
	}

	function getVersion( $extension, $xml )
	{
		if ( !$extension || !$xml ) {
			return;
		}

		$version = '';
		if ( $xml ) {
			$xml = JApplicationHelper::parseXMLInstallFile( JPATH_SITE.'/'.$xml );
			if ( $xml && isset( $xml['version'] ) ) {
				$version = $xml['version'];
			}
		}
		return $version;
	}

	static function getXMLVersion( $extension = 'nnframework', $type = 'system', $admin = 1, $urlformat = 0 )
	{
		if ( !$extension ) {
			$extension = 'nnframework';
		}
		if ( !$type ) {
			$type = 'system';
		}
		if ( !strlen( $admin ) ) {
			$admin = 1;
		}

		switch ( $type ) {
			case 'component':
			case 'components':
			case 'module':
			case 'modules':
				$type .= in_array( $type, array( 'component', 'module' ) ) ? 's' : '';
				if ( $admin ) {
					$path = JPATH_ADMINISTRATOR;
				} else {
					$path = JPATH_SITE;
				}
				$path .= '/'.$type.'/'.( $type == 'modules' ? 'mod_' : 'com_' ).$extension.'/'.( $type == 'modules' ? 'mod_' : '' ).$extension.'.xml';
				break;
			default:
				$path = JPATH_SITE.'/plugins/'.$type.'/'.$extension.'/'.$extension.'.xml';
				break;
		}

		$version = '';
		$xml = JApplicationHelper::parseXMLInstallFile( $path );
		if ( $xml && isset( $xml['version'] ) ) {
			$version = trim( strtolower( $xml['version'] ) );
			if ( $urlformat ) {
				$version = '?v='.$version;
			}
		}

		return $version;
	}

	function setMessage( $current_version = '0', $version_file = '' )
	{
		echo $this->getMessage( str_replace( 'version_', '', $version_file ), '', $current_version, 1 );
	}
}