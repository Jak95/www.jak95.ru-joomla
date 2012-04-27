<?php
/**
 * NoNumber! Framework Helper File: Assignments: K2
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
 * Assignments: K2
 */
class NNFrameworkAssignmentsK2
{
	var $_version = '12.1.6';

	/**
	 * passCategories_K2
	 *
	 * @param <object> $params
	 * inc_children
	 * inc_categories
	 * inc_items
	 * @param <array> $selection
	 * @param <string> $assignment
	 *
	 * @return <bool>
	 */
	function passCategories_K2( &$main, &$params, $selection = array(), $assignment = 'all', $article = 0 )
	{
		if ( $main->_params->option != 'com_k2' ) {
			return ( $assignment == 'exclude' );
		}

		$pass = (
			( $params->inc_categories && $main->_params->view == 'itemlist' && $main->_params->task == 'category' )
				|| ( $params->inc_items && $main->_params->view == 'item' )
		);

		if ( !$pass ) {
			return ( $assignment == 'exclude' );
		}

		$selection = $main->makeArray( $selection );

		if ( $article && isset( $article->catid ) ) {
			$cats = $article->catid;
		} else {
			switch ( $main->_params->view ) {
				case 'itemlist':
					$cats = $main->_params->id;
					break;
				case 'item':
				default:
					$query = 'SELECT catid'
						.' FROM #__k2_items'
						.' WHERE id = '.(int) $main->_params->id
						.' LIMIT 1';
					$main->_db->setQuery( $query );
					$cats = $main->_db->loadResult();
					break;
			}
		}

		$cats = $main->makeArray( $cats, 1 );

		$pass = $main->passSimple( $cats, $selection, 'include' );

		if ( $pass && $params->inc_children == 2 ) {
			return ( $assignment == 'exclude' );
		} else if ( !$pass && $params->inc_children ) {
			foreach ( $cats as $cat ) {
				$cats = array_merge( $cats, NNFrameworkAssignmentsK2::getCatParentIds( $main, $cat ) );
			}
		}

		return $main->passSimple( $cats, $selection, $assignment );
	}

	/**
	 * passTags_K2
	 *
	 * @param <object> $params
	 * @param <array> $selection
	 * @param <string> $assignment
	 *
	 * @return <bool>
	 */
	function passTags_K2( &$main, &$params, $selection = array(), $assignment = 'all' )
	{
		if ( $main->_params->option != 'com_k2' ) {
			return ( $assignment == 'exclude' );
		}

		$tag = trim( JRequest::getString( 'tag' ) );
		$pass = (
			( $params->inc_tags && $tag != '' )
				|| ( $params->inc_items && $main->_params->view == 'item' )
		);

		if ( !$pass ) {
			return ( $assignment == 'exclude' );
		}

		$selection = $main->makeArray( $selection );

		if ( $params->inc_tags && $tag != '' ) {
			$tags = array( trim( JRequest::getString( 'tag' ) ) );
		} else {
			$query = 'SELECT t.name'
				.' FROM #__k2_tags_xref as x'
				.' LEFT JOIN #__k2_tags as t'
				.' ON t.id = x.tagID'
				.' WHERE x.itemID = '.(int) $main->_params->id
				.' AND t.published = 1';
			$main->_db->setQuery( $query );
			$tags = $main->_db->loadResultArray();
		}

		return $main->passSimple( $tags, $selection, $assignment, 1 );
	}

	/**
	 * passItems_K2
	 *
	 * @param <object> $params
	 * @param <array> $selection
	 * @param <string> $assignment
	 *
	 * @return <bool>
	 */
	function passItems_K2( &$main, &$params, $selection = array(), $assignment = 'all' )
	{
		if ( !$main->_params->id || $main->_params->option != 'com_k2' || $main->_params->view != 'item' ) {
			return ( $assignment == 'exclude' );
		}

		$selection = $main->makeArray( $selection );

		return $main->passSimple( array( $main->_params->id ), $selection, $assignment );
	}

	function getCatParentIds( &$main, $id = 0 )
	{
		return $main->getParentIds( $id, 'k2_categories' );
	}
}