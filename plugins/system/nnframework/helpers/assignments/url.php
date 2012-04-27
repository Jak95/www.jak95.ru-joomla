<?php
/**
 * NoNumber! Framework Helper File: Assignments: URL
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
 * Assignments: URL
 */
class NNFrameworkAssignmentsURL
{
	var $_version = '12.1.6';

	/**
	 * passURL
	 *
	 * @param <object> $params
	 * @param <array> $selection
	 * @param <string> $assignment
	 *
	 * @return <bool>
	 */
	function passURL( &$main, &$params, $selection = array(), $assignment = 'all' )
	{
		$url = JFactory::getURI();
		$url = $url->toString();

		if ( !is_array( $selection ) ) {
			$selection = explode( "\n", $selection );
		}

		$pass = 0;
		foreach ( $selection as $url_part ) {
			if ( $url_part !== '' ) {
				$url_part = trim( str_replace( '&amp;', '(&amp;|&)', $url_part ) );
				$s = '#'.$url_part.'#si';
				if ( @preg_match( $s.'u', $url )
					|| @preg_match( $s.'u', html_entity_decode( $url, ENT_COMPAT, 'UTF-8' ) )
					|| @preg_match( $s, $url )
					|| @preg_match( $s, html_entity_decode( $url, ENT_COMPAT, 'UTF-8' ) )
				) {
					$pass = 1;
					break;
				}
			}
		}

		if ( $pass ) {
			return ( $assignment == 'include' );
		} else {
			return ( $assignment == 'exclude' );
		}
	}

	/**
	 * passHomePage
	 *
	 * @param <object> $params
	 * @param <array> $selection
	 * @param <string> $assignment
	 *
	 * @return <bool>
	 */
	function passHomePage( &$main, &$params, $selection = array(), $assignment = 'all', $addlang = 0 )
	{
		$app = JFactory::getApplication();

		$pass = 0;

		$uri = JFactory::getURI();

		if ( $addlang ) {
			$sef = $uri->getVar( 'lang' );
			if ( empty( $sef ) ) {
				$langs = array_keys( JLanguageHelper::getLanguages( 'sef' ) );
				$path = JString::substr( $uri->toString(), JString::strlen( $uri->base() ) );
				$path = JFactory::getConfig()->get( 'sef_rewrite' ) ? $path : JString::substr( $path, 10 );
				$parts = explode( '/', $path );
				$part = reset( $parts );
				if ( in_array( $part, $langs ) )  {
					$sef = $part;
				}
			}
			if ( empty( $sef ) ) {
				return 0;
			}
		}

		$url = $uri->toString();

		// replace ampersand chars
		$url = str_replace( '&amp;', '&', $url );
		// remove trailing nonsense
		$url = trim( preg_replace( '#/?\??&?$#', '', $url ) );
		// remove the www.
		$url = str_replace( '//www.', '//', $url );
		// remove the http(s)
		$url = preg_replace( '#^.*?://#', '', $url );
		// remove the index.php/
		$url = preg_replace( '#/index\.php(/|$)#', '/', $url );

		// so also passes on urls with trailing /, ?, &, /?, etc...
		$root = preg_replace( '#(Itemid=[0-9]*).*^#', '\1', JURI::root() );
		// remove trailing /
		$root = trim( preg_replace( '#/$#', '', $root ) );
		// remove the www.
		$root = str_replace( '//www.', '//', $root );
		// remove the http(s)
		$root = preg_replace( '#^.*?://#', '', $root );

		if ( $addlang ) {
			$root .= '/'.$sef;
		}

		if ( !$pass ) {
			/* Pass urls:
			 * [root]
			 */
			$regex = '#^'.$root.'$#i';
			$pass = preg_match( $regex, $url );
		}

		if ( !$pass ) {
			$menu = $app->getMenu( 'site' );
			$menu_def = $menu->getDefault();
			/* Pass urls:
			 * [root]?Itemid=[menu-id]
			 * [root]/?Itemid=[menu-id]
			 * [root]/index.php?Itemid=[menu-id]
			 * [root]/[menu-alias]
			 * [root]/[menu-alias]?Itemid=[menu-id]
			 * [root]/index.php?[menu-alias]
			 * [root]/index.php?[menu-alias]?Itemid=[menu-id]
			 * [root]/[menu-link]
			 * [root]/[menu-link]&Itemid=[menu-id]
			 */
			$regex = '#^'.$root
				.'(/('
				.'index\.php'
				.'|'
				.'(index\.php\?)?'.preg_quote( $menu_def->alias, '#' )
				.'|'
				.preg_quote( $menu_def->link, '#' )
				.')?)?'
				.'(/?[\?&]Itemid='.(int) $menu_def->id.')?'
				.'$#i';
			$pass = preg_match( $regex, $url );
		}

		if ( !$pass && !$addlang ) {
			$pass = $this->passHomePage( $main, $params, $selection, $assignment, 1 );
		}

		if ( $pass ) {
			return ( $assignment == 'include' );
		} else {
			return ( $assignment == 'exclude' );
		}
	}
}