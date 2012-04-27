<?php
/**
 * Element: Components
 * Displays a list of components with check boxes
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
 * Components Element
 */
class nnFieldComponents
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		$this->params = $params;

		$frontend = $this->def( 'frontend', 1 );
		$admin = $this->def( 'admin', 1 );
		$show_content = $this->def( 'show_content', 0 );
		$size = (int) $this->def( 'size' );

		$components = $this->getComponents( $frontend, $admin, $show_content, $j15 );

		$options = array();

		$comps = array();
		$lang = JFactory::getLanguage();
		foreach ( $components as $component ) {
			if ( !$j15 ) {
				if ( !empty( $component->option ) ) {
					// Load the core file then
					// Load extension-local file.
					$lang->load( $component->option.'.sys', JPATH_BASE, null, false, false )
						|| $lang->load( $component->option.'.sys', JPATH_ADMINISTRATOR.'/components/'.$component->option, null, false, false )
						|| $lang->load( $component->option.'.sys', JPATH_BASE, $lang->getDefault(), false, false )
						|| $lang->load( $component->option.'.sys', JPATH_ADMINISTRATOR.'/components/'.$component->option, $lang->getDefault(), false, false );
				}
				$component->name = JText::_( strtoupper( $component->name ) );
			}
			$comps[preg_replace( '#[^a-z0-9_]#i', '', $component->name.'_'.$component->option )] = $component;
		}
		ksort( $comps );
		foreach ( $comps as $component ) {
			$options[] = JHtml::_( 'select.option', $component->option, $component->name, 'value', 'text' );
		}

		require_once JPATH_PLUGINS.'/system/nnframework/helpers/html.php';
		return nnHTML::selectlist( $options, $name, $value, $id, $size, 1, '', $j15 );
	}

	function getComponents( $frontend = 1, $admin = 1, $show_content = 0, $j15 = 0 )
	{
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );

		$db = JFactory::getDBO();

		if ( $j15 ) {
			$from = '#__components';
			$where = 'enabled = 1';
			$select_id = 'id';
			$select_option = $db->nameQuote( 'option' );
		} else {
			$from = '#__extensions';
			$where = 'type = '.$db->quote( 'component' ).' AND enabled = 1';
			$select_id = 'extension_id';
			$select_option = $db->nameQuote( 'element' );
		}

		if ( !$frontend && !$admin ) {
			$query = 'SELECT '.$select_option.' AS '.$db->nameQuote( 'option' ).', name'
				.' FROM '.$from
				.' WHERE '.$where;
			if ( $j15 ) {
				$query .= 'AND parent = 0';
			}
			if ( !$show_content ) {
				$query .= ' AND '.$select_option.' <> "com_content"';
			}
			$query .= ' ORDER BY name';
			$db->setQuery( $query );
			return $db->loadObjectList();
		} else {
			if ( $frontend ) {
				if ( $j15 ) {
					// component subs
					$query = 'SELECT parent'
						.' FROM '.$from
						.' WHERE '.$where
						.' AND parent != 0'
						.' AND link != ""'
						.' GROUP BY parent';
					$db->setQuery( $query );
					$subcomponents = $db->loadResultArray();

					// main components
					$query = 'SELECT '.$select_id.' AS id'
						.' FROM '.$from
						.' WHERE '.$where
						.' AND parent = 0'
						.' AND ( link != ""';
					if ( !empty( $subcomponents ) ) {
						$query .= ' OR id IN ( '.implode( ',', $subcomponents ).' )';
					}
					$query .= ' )'
						.' ORDER BY ordering, name';
					$db->setQuery( $query );
					$component_ids = $db->loadResultArray();
				} else if ( !$admin ) {
					$query = 'SELECT '.$select_id.' AS id, name, element'
						.' FROM '.$from
						.' WHERE '.$where
						.' ORDER BY ordering, name';
					$db->setQuery( $query );
					$component_ids = $db->loadObjectList( 'id' );

					foreach ( $component_ids as $id => $component ) {
						$name = 'com_'.preg_replace( '#^com_#', '', $component->element );
						$path = JPATH_SITE.'/components/'.$name;
						if ( JFile::exists( $path.'/metadata.xml' ) ) {
							continue;
						}
						$pass = 0;
						if ( JFolder::exists( $path.'/views' ) ) {
							$views = JFolder::folders( $path.'/views' );
							foreach ( $views as $view ) {
								$file = $path.'/views/'.$view.'/tmpl/default.xml';
								if ( !JFile::exists( $file ) ) {
									$file = $path.'/views/'.$view.'/metadata.xml';
									if ( !JFile::exists( $file ) ) {
										continue;
									}
								}
								$xml = simplexml_load_file( $file );
								if ( !$xml || ( !isset( $xml->layout ) && !isset( $xml->view ) ) ) {
									continue;
								}
								$view = isset( $xml->layout ) ? $xml->layout : $xml->view;
								if ( isset( $view->attributes()->hidden ) && (string) $view->attributes()->hidden == 'true' ) {
									continue;
								}
								$pass = 1;
								break;
							}
						}

						if ( !$pass ) {
							unset( $component_ids[$id] );
						}
					}
					$component_ids = array_keys( $component_ids );
				}
			}

			if ( $admin ) {
				if ( $j15 ) {
					// component subs
					$query = 'SELECT parent'
						.' FROM '.$from
						.' WHERE '.$where
						.' AND parent != 0'
						.' AND admin_menu_link != ""';
					$db->setQuery( $query );
					$subcomponents = $db->loadResultArray();
					$subcomponents = array_unique( $subcomponents );

					// main components
					$query = 'SELECT '.$select_id.' AS id'
						.' FROM '.$from
						.' WHERE '.$where
						.' AND parent = 0'
						.' AND ( admin_menu_link != ""';
					if ( !empty( $subcomponents ) ) {
						$query .= ' OR id IN ( '.implode( ',', $subcomponents ).' )';
					}
					$query .= ' )';
				} else {
					$query = 'SELECT '.$select_id.' AS id'
						.' FROM '.$from
						.' WHERE '.$where;
				}
				$db->setQuery( $query );
				if ( $frontend && isset( $component_ids ) ) {
					$component_ids = array_merge( $component_ids, $db->loadResultArray() );
				} else {
					$component_ids = $db->loadResultArray();
				}
			}

			$component_ids = array_unique( $component_ids );
			$query = 'SELECT '.$select_option.' AS '.$db->nameQuote( 'option' ).', name'
				.' FROM '.$from
				.' WHERE '.$where;
			if ( $j15 ) {
				$query .= ' AND parent = 0';
			}
			if ( !empty( $component_ids ) ) {
				$query .= ' AND '.$select_id.' IN ( '.implode( ',', $component_ids ).' )';
			}
			if ( !$show_content ) {
				$query .= ' AND '.$select_option.' <> "com_content"';
			}
			$query .= ' ORDER BY name';
			$db->setQuery( $query );

			return $db->loadObjectList();
		}
	}

	private function def( $val, $default = '' )
	{
		return ( isset( $this->params[$val] ) && (string) $this->params[$val] != '' ) ? (string) $this->params[$val] : $default;
	}
}

if ( version_compare( JVERSION, '1.6.0', 'l' ) ) {
	// For Joomla 1.5
	class JElementNN_Components extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'Components';

		function fetchElement( $name, $value, &$node, $control_name )
		{
			$this->_nnfield = new nnFieldComponents();
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_Components extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'Components';

		protected function getInput()
		{
			$this->_nnfield = new nnFieldComponents();
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}