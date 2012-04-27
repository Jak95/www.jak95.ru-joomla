<?php
/**
 * Plugin Helper File
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

/**
 * ...
 */
class plgSystemNNFrameworkHelper
{
	function __construct()
	{
		$app = JFactory::getApplication();

		$url = JRequest::getVar( 'url' );
		$options = JRequest::getVar( 'url_options', array(), 'post', 'array' );
		$func = new plgSystemNNFrameworkHelperFunctions;

		if ( $url ) {
			echo $func->getByUrl( $url, $options );
			exit();
		}

		$file = JRequest::getVar( 'file' );

		// only allow files that have .inc.php in the file name
		if ( !$file || ( strpos( $file, '.inc.php' ) === false ) ) {
			die();
		}

		$folder = JRequest::getVar( 'folder' );
		if ( $folder ) {
			$file = implode( '/', explode( '.', $folder ) ).'/'.$file;
		}

		$allowed = array(
			'administrator/components/com_dbreplacer/dbreplacer.inc.php',
			'administrator/components/com_nonumbermanager/details.inc.php',
			'administrator/components/com_rereplacer/images/image.inc.php',
			'administrator/modules/mod_addtomenu/addtomenu/addtomenu.inc.php',
			'plugins/editors-xtd/articlesanywhere/articlesanywhere.inc.php',
			'plugins/editors-xtd/contenttemplater/contenttemplater.inc.php',
			'plugins/editors-xtd/modulesanywhere/modulesanywhere.inc.php',
			'plugins/editors-xtd/snippets/snippets.inc.php',
			'plugins/editors-xtd/sourcerer/sourcerer.inc.php'
		);

		if ( !$file || ( in_array( $file, $allowed ) === false ) ) {
			die();
		}

		jimport( 'joomla.filesystem.file' );

		if ( $app->isSite() && !JRequest::getCmd( 'usetemplate' ) ) {
			$app->setTemplate( 'system' );
		}
		$_REQUEST['tmpl'] = 'component';
		JRequest::setVar( 'option', '1' );

		$app->set( '_messageQueue', '' );

		$file = JPATH_SITE.'/'.$file;

		$html = '';
		if ( JFile::exists( $file ) ) {
			ob_start();
			include $file;
			$html = ob_get_contents();
			ob_end_clean();
		}

		$document = JFactory::getDocument();
		$document->setBuffer( $html, 'component' );
		$document->addStyleSheet( JURI::root( true ).'/templates/system/css/system.css' );
		$document->addStyleSheet( JURI::root( true ).'/plugins/system/nnframework/css/default.css' );
		$document->addScript( JURI::root( true ).'/includes/js/joomla.javascript.js' );
		if ( version_compare( JVERSION, '1.6.0', 'l' ) ) {
			$document->addStyleSheet( JURI::root( true ).'/administrator/templates/khepri/css/icon.css' );
			$document->addStyleSheet( JURI::root( true ).'/administrator/templates/khepri/css/rounded.css' );
		}

		$app->render();

		echo JResponse::toString( $app->getCfg( 'gzip' ) );

		exit();
	}
}

class plgSystemNNFrameworkHelperFunctions
{
	function getByUrl( $url, $options = array() )
	{

		if ( substr( $url, 0, 4 ) != 'http' ) {
			$url = 'http://'.$url;
		}
		if ( !( strpos( $url, 'http://www.nonumber.nl/' ) === 0 ) ) {
			die();
		}

		$html = '';
		if ( function_exists( 'curl_init' ) ) {
			$html = $this->curl( $url, $options );
		} else {
			$file = @fopen( $url, 'r' );
			if ( $file ) {
				$html = array();
				while ( !feof( $file ) ) {
					$html[] = fgets( $file, 1024 );
				}
				$html = implode( '', $html );
			}
		}

		return $html;
	}

	function curl( $url, $options = array() )
	{
		$ch = curl_init( $url );
		$ch_options = array(
			CURLOPT_URL			=> $url,
			CURLOPT_HEADER		 => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT		=> 3,
			CURLOPT_USERAGENT	  => 'some crazy browser'
		);

		if ( !empty( $options ) ) {
			$curl_opts = $this->getCurlOpts();
			foreach ( $options as $key => $option ) {
				if ( !is_numeric( $key ) ) {
					if ( !isset( $curl_opts[$key] ) ) {
						continue;
					}
					$key = $curl_opts[$key];
				}
				$ch_options[$key] = $option;
			}
		}

		curl_setopt_array( $ch, $ch_options );

		//follow on location problems
		if ( ini_get( 'open_basedir' ) == '' && ini_get( 'safe_mode' ) != 'On' ) {
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
			$html = curl_exec( $ch );
		} else {
			$html = $this->curl_redir_exec( $ch );
		}
		curl_close( $ch );
		return $html;
	}

	function curl_redir_exec( $ch )
	{
		static $curl_loops = 0;
		static $curl_max_loops = 20;

		if ( $curl_loops++ >= $curl_max_loops ) {
			$curl_loops = 0;
			return false;
		}

		curl_setopt( $ch, CURLOPT_HEADER, true );
		$data = curl_exec( $ch );

		list( $header, $data ) = explode( "\n\n", str_replace( "\r", '', $data ), 2 );
		$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

		if ( $http_code == 301 || $http_code == 302 ) {
			$matches = array();
			preg_match( '/Location:(.*?)\n/', $header, $matches );
			$url = @parse_url( trim( array_pop( $matches ) ) );
			if ( !$url ) {
				//couldn't process the url to redirect to
				$curl_loops = 0;
				return $data;
			}
			$last_url = parse_url( curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL ) );
			if ( !$url['scheme'] ) {
				$url['scheme'] = $last_url['scheme'];
			}
			if ( !$url['host'] ) {
				$url['host'] = $last_url['host'];
			}
			if ( !$url['path'] ) {
				$url['path'] = $last_url['path'];
			}
			$new_url = $url['scheme'].'://'.$url['host'].$url['path'].( $url['query'] ? '?'.$url['query'] : '' );
			curl_setopt( $ch, CURLOPT_URL, $new_url );
			return $this->curl_redir_exec( $ch );
		} else {
			$curl_loops = 0;
			return $data;
		}
	}

	function getCurlOpts()
	{
		return array(
			'CURLOPT_PROXY'		=> 10004,
			'CURLOPT_PROXYPORT'	=> 59,
			'CURLOPT_PROXYTYPE'	=> 101,
			'CURLOPT_PROXYUSERPWD' => 10006,
			'CURLOPT_TIMEOUT'	  => 13,
			'CURLOPT_TIMEOUT_MS'   => 155,
			'CURLOPT_TIMEVALUE'	=> 34
		);
	}
}