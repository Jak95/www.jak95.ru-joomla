<?php
/**
 * NoNumber! Framework Helper File: Assignments
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
 * Assignments
 * $assignment = no / include / exclude / none
 */
class NNFrameworkAssignmentsHelper
{
	var $_version = '12.1.6';

	var $_db = null;
	var $_params = null;
	var $_types = array();

	function __construct()
	{
		$this->_db = JFactory::getDBO();

		$config = JFactory::getConfig();
		$this->_date = JFactory::getDate();
		$this->_date->setTimeZone( new DateTimeZone( $config->getValue( 'config.offset' ) ) );

		$this->_types = array(
			'MenuItem',
			'HomePage',
			'Cats',
			'Articles',
			'Categories_FC',
			'Tags_FC',
			'Types_FC',
			'Items_FC',
			'Categories_K2',
			'Tags_K2',
			'Items_K2',
			'Categories_MR',
			'Categories_ZOO',
			'Components',
			'URL',
			'Browsers',
			'Date',
			'Seasons',
			'Months',
			'Days',
			'Time',
			'UserGroupLevels',
			'Users',
			'Languages',
			'Templates',
			'PHP'
		);
		$this->_classes = array();
	}

	function getRequestParams()
	{
		$params = new stdClass();
		$params->option = JRequest::getCmd( 'option' );
		$params->view = JRequest::getCmd( 'view' );
		$params->task = JRequest::getCmd( 'task' );
		$params->id = JRequest::getInt( 'id' );
		$params->Itemid = JRequest::getInt( 'Itemid' );

		switch ( $params->option ) {
			case 'com_categories':
				$params->option = 'com_content';
				$params->view = 'category';
				break;
			case 'com_sections':
				$params->option = 'com_content';
				$params->view = 'section';
				break;
			case 'com_mr':
				$params->item_id = JRequest::getInt( 'article' );
				$params->category_id = JRequest::getInt( 'category_id' );
				$params->id = ( $params->item_id ) ? $params->item_id : $params->category_id;
				break;
			case 'com_zoo':
				$params->item_id = JRequest::getInt( 'item_id' );
				$params->category_id = JRequest::getInt( 'category_id' );
				$params->id = ( $params->item_id ) ? $params->item_id : $params->category_id;
				break;
		}

		if ( !$params->id ) {
			$cid = JRequest::getVar( 'cid', array( 0 ), 'method', 'array' );
			$cid = array( (int) $cid['0'] );
			$params->id = $cid['0'];
		}

		return $params;
	}

	function initParams( &$params, $type = '' )
	{
		if ( !isset( $params->assignment ) ) {
			$params->assignment = 'all';
		} else {
			$this->getAssignmentState( $params->assignment );
		}

		if ( !isset( $params->selection ) ) {
			$params->selection = array();
		}
		if ( !isset( $params->params ) ) {
			$params->params = null;
		}

		$this->maintype = $type;
		switch ( $type ) {
			case 'MenuItem':
				$this->maintype = 'Menu';
				break;
			case 'HomePage':
				$this->maintype = 'URL';
				break;
			case 'Cats':
			case 'Articles':
				$this->maintype = 'Content';
				break;
			case 'Categories_FC':
			case 'Tags_FC':
			case 'Types_FC':
			case 'Items_FC':
				$this->maintype = 'FlexiContent';
				break;
			case 'Categories_K2':
			case 'Tags_K2':
			case 'Items_K2':
				$this->maintype = 'K2';
				break;
			case 'Categories_MR':
				$this->maintype = 'Resources';
				break;
			case 'Categories_ZOO':
				$this->maintype = 'ZOO';
				break;
			case 'Date':
			case 'Seasons':
			case 'Months':
			case 'Days':
			case 'Time':
				$this->maintype = 'DateTime';
				break;
			case 'UserGroupLevels':
			case 'Users':
				$this->maintype = 'Users';
				break;
		}

		switch ( $type ) {
			case 'MenuItem':
				if ( !isset( $params->params->inc_children ) ) {
					$params->params->inc_children = 0;
				}
				if ( !isset( $params->params->inc_noItemid ) ) {
					$params->params->inc_noItemid = 0;
				}
				break;
			case 'Articles':
				if ( !isset( $params->params->keywords ) ) {
					$params->params->keywords = '';
				}
				break;
			case 'Cats':
				if ( !isset( $params->params->inc_children ) ) {
					$params->params->inc_children = 1;
				}
				if ( !isset( $params->params->inc_sections ) ) {
					$params->params->inc_sections = 1;
				}
				if ( !isset( $params->params->inc_categories ) ) {
					$params->params->inc_categories = 1;
				}
				if ( !isset( $params->params->inc_articles ) ) {
					$params->params->inc_articles = 1;
				}
				if ( !isset( $params->params->inc_others ) ) {
					$params->params->inc_others = 0;
				}
				break;
			case 'Categories_FC':
			case 'Categories_K2':
			case 'Categories_MR':
			case 'Categories_ZOO':
				if ( !isset( $params->params->inc_children ) ) {
					$params->params->inc_children = 0;
				}
				if ( !isset( $params->params->inc_categories ) ) {
					$params->params->inc_categories = 1;
				}
				if ( !isset( $params->params->inc_items ) ) {
					$params->params->inc_items = 1;
				}
				break;
			case 'Tags_FC':
			case 'Tags_K2':
				if ( !isset( $params->params->inc_tags ) ) {
					$params->params->inc_tags = 1;
				}
				if ( !isset( $params->params->inc_items ) ) {
					$params->params->inc_items = 1;
				}
				break;
			case 'Date':
			case 'Time':
				if ( !isset( $params->params->publish_up ) ) {
					$params->params->publish_up = 0;
				}
				if ( !isset( $params->params->publish_down ) ) {
					$params->params->publish_down = 0;
				}
				break;
			case 'Seasons':
				if ( !isset( $params->params->hemisphere ) ) {
					$params->params->hemisphere = 'northern';
				}
				break;
		}
	}

	function passAll( &$params, $match_method = 'and', $article = 0 )
	{
		if ( empty( $params ) ) {
			return 1;
		}

		jimport( 'joomla.filesystem.file' );

		$app = JFactory::getApplication();
		$this->_params = $this->getRequestParams();

		// if no id is found, check if menuitem exists to get view and id
		if ( $app->isSite() && ( !$this->_params->option || !$this->_params->id ) ) {
			$menu = $app->getMenu( 'site' );
			if ( empty( $this->_params->Itemid ) ) {
				$menuItem = $menu->getActive();
			} else {
				$menuItem = $menu->getItem( $this->_params->Itemid );
			}
			if ( !$this->_params->option ) {
				$this->_params->option = ( empty( $menuItem->query['option'] ) ) ? null : $menuItem->query['option'];
			}
			$this->_params->view = ( empty( $menuItem->query['view'] ) ) ? null : $menuItem->query['view'];
			$this->_params->task = ( empty( $menuItem->query['task'] ) ) ? null : $menuItem->query['task'];
			if ( !$this->_params->id ) {
				$this->_params->id = ( empty( $menuItem->query['id'] ) ) ? null : $menuItem->query['id'];
			}
			unset( $menuItem );
		}

		$pass = ( $match_method == 'and' ) ? 1 : 0;
		foreach ( $this->_types as $type ) {
			if ( isset( $params[$type] ) ) {
				$this->initParams( $params[$type], $type );
				if ( ( $pass && $match_method == 'and' ) || ( !$pass && $match_method == 'or' ) ) {
					if ( $params[$type]->assignment == 'all' ) {
						$pass = 1;
					} else if ( $params[$type]->assignment == 'none' ) {
						$pass = 0;
					} else {
						if ( !isset( $this->_classes[$this->maintype] ) && JFile::exists( dirname( __FILE__ ).'/assignments/'.strtolower( $this->maintype ).'.php' ) ) {
							require_once dirname( __FILE__ ).'/assignments/'.strtolower( $this->maintype ).'.php';
							$class = 'NNFrameworkAssignments'.$this->maintype;
							$this->_classes[$this->maintype] = new $class;
						}
						if ( isset( $this->_classes[$this->maintype] ) ) {
							$func = 'pass'.$type;
							$pass = $this->_classes[$this->maintype]->$func( $this, $params[$type]->params, $params[$type]->selection, $params[$type]->assignment, $article );
						}
					}
				}
			}
		}
		return ( $pass ) ? 1 : 0;
	}

	/**
	 * passSimple
	 *
	 * @param <string> $value
	 * @param <array> $selection
	 * @param <string> $assignment
	 *
	 * @return <bool>
	 */
	function passSimple( $values = '', $selection = array(), $assignment = 'all', $caseinsensitive = 0 )
	{
		$values = $this->makeArray( $values, 1 );
		$selection = $this->makeArray( $selection );

		$pass = 0;
		foreach ( $values as $value ) {
			if ( $caseinsensitive ) {
				if ( in_array( strtolower( $value ), array_map( 'strtolower', $selection ) ) ) {
					$pass = 1;
					break;
				}
			} else {
				if ( in_array( $value, $selection ) ) {
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
	 * getAssignmentState
	 *
	 * @param <string> $assignment
	 */
	function getAssignmentState( &$assignment )
	{
		switch ( $assignment ) {
			case 1:
			case 'include':
				$assignment = 'include';
				break;
			case 2:
			case 'exclude':
				$assignment = 'exclude';
				break;
			case 3:
			case -1:
			case 'none':
				$assignment = 'none';
				break;
			default:
				$assignment = 'all';
				break;
		}
	}

	function getMenuItemParams( $id = 0 )
	{
		$query = 'SELECT params'
			.' FROM #__menu'
			.' WHERE id = '.(int) $id
			.' LIMIT 1';
		$this->_db->setQuery( $query );
		$params = $this->_db->loadResult();

		$parameters = NNParameters::getInstance();
		return $parameters->getParams( $params );
	}

	function getParentIds( $id = 0, $table = 'menu', $name = 'parent_id' )
	{
		$parent_ids = array();

		if ( !$id ) {
			return $parent_ids;
		}

		while ( $id ) {
			$query = 'SELECT '.$name
				.' FROM #__'.$table
				.' WHERE id = '.(int) $id
				.' LIMIT 1';
			$this->_db->setQuery( $query );
			$id = $this->_db->loadResult();
			if ( $id ) {
				$parent_ids[] = $id;
			}
		}
		return $parent_ids;
	}

	/**
	 * makeArray
	 *
	 * @param <array> $array
	 * @param <boolean> $onlycommas
	 */
	function makeArray( $array = '', $onlycommas = 0, $trim = 1 )
	{
		if ( !is_array( $array ) ) {
			if ( !$onlycommas && !( strpos( $array, '|' ) === false ) ) {
				$array = explode( '|', $array );
			} else {
				$array = explode( ',', $array );
			}
		}
		if ( $trim ) {
			$trim_values = create_function( '&$val', '$val = trim( $val );' );
			array_walk( $array, $trim_values );
		}
		return $array;
	}

	function passMenuItem( &$params, $selection = array(), $assignment = 'all' )
	{
		if ( !isset( $this->_classes['Menu'] ) && JFile::exists( dirname( __FILE__ ).'/assignments/menu.php' ) ) {
			require_once dirname( __FILE__ ).'/assignments/menu.php';
			$this->_classes[$this->maintype] = new NNFrameworkAssignmentsMenu;
		}
		if ( isset( $this->_classes['Menu'] ) ) {
			return $this->_classes['Menu']->passMenuItem( $this, $params, $selection, $assignment );
		}
		return 1;
	}
}