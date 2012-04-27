<?php
/**
 * NoNumber! Framework Helper File: Assignments: Browsers
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
 * Assignments: Browsers
 */
class NNFrameworkAssignmentsBrowsers
{
	var $_version = '12.1.6';

	/**
	 * passBrowsers
	 *
	 * @param <object> $params
	 * @param <array> $selection
	 * @param <string> $assignment
	 *
	 * @return <bool>
	 */
	function passBrowsers( &$main, &$params, $selection = array(), $assignment = 'all' )
	{
		$pass = 0;

		$selection = $main->makeArray( $selection );

		if ( !empty( $selection ) ) {
			jimport( 'joomla.environment.browser' );
			$browser = JBrowser::getInstance();
			$b = $browser->getAgentString();
			if ( !( stripos( $b, 'Chrome' ) === false ) ) {
				$b = preg_replace( '#(Chrome/.*)Safari/[0-9\.]*#is', '\1', $b );
			} else if ( !( stripos( $b, 'Opera' ) === false ) ) {
				$b = preg_replace( '#(Opera/.*)Version/#is', '\1Opera/', $b );
			}
			foreach ( $selection as $sel ) {
				if ( $sel && !( stripos( $b, $sel ) === false ) ) {
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
}