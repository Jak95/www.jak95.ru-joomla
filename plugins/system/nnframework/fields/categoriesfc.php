<?php
/**
 * Element: CategoriesFC
 * Displays a multiselectbox of available Flexicontent categories
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
 * CategoriesFC Element
 */
class nnFieldCategoriesFC
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		$this->params = $params;

		if ( !file_exists( JPATH_ADMINISTRATOR.'/components/com_flexicontent/admin.flexicontent.php' ) ) {
			return 'Flexicontent files not found...';
		}

		$db = JFactory::getDBO();
		$sql = "SHOW tables like '".$db->getPrefix()."flexicontent_cats_item_relations'";
		$db->setQuery( $sql );
		$tables = $db->loadObjectList();

		if ( !count( $tables ) ) {
			return 'Flexicontent category-item relations table not found in database...';
		}

		$size = (int) $this->def( 'size' );
		$multiple = $this->def( 'multiple' );
		$get_categories = $this->def( 'getcategories', 1 );
		$show_ignore = $this->def( 'show_ignore' );

		if ( !is_array( $value ) ) {
			$value = explode( ',', $value );
		}

		$flexicomp_params = JComponentHelper::getParams( 'com_flexicontent' );
		$flexi_section = $flexicomp_params->get( 'flexi_section' );

		$sql = 'SELECT  id, parent_id as parent, title as name'
			.' FROM #__categories'
			.' WHERE published = 1'
			.' AND section = '.$flexi_section
			.' ORDER BY ordering';
		$db->setQuery( $sql );
		$menuItems = $db->loadObjectList();

		// establish the hierarchy of the menu
		// TODO: use node model
		$children = array();

		if ( $menuItems ) {
			// first pass - collect children
			foreach ( $menuItems as $v ) {
				$pt = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		require_once JPATH_LIBRARIES.'/joomla/html/html/menu.php';
		$list = JHTMLMenu::treerecurse( 0, '', array(), $children, 9999, 0, 1 );

		// assemble items to the array
		$options = array();
		if ( $show_ignore ) {
			if ( in_array( '-1', $value ) ) {
				$value = array( '-1' );
			}
			$options[] = JHtml::_( 'select.option', '-1', '- '.JText::_( 'NN_IGNORE' ).' -', 'value', 'text', 0 );
		}
		foreach ( $list as $item ) {
			$item_name = preg_replace( '#^((&nbsp;)*)- #', '\1', str_replace( '&#160;', '&nbsp;', $item->treename ) );
			$options[] = JHtml::_( 'select.option', $item->id, $item_name, 'value', 'text', 0 );
		}

		require_once JPATH_PLUGINS.'/system/nnframework/helpers/html.php';
		return nnHTML::selectlist( $options, $name, $value, $id, $size, $multiple, '', $j15 );
	}

	private function def( $val, $default = '' )
	{
		return ( isset( $this->params[$val] ) && (string) $this->params[$val] != '' ) ? (string) $this->params[$val] : $default;
	}
}

if ( version_compare( JVERSION, '1.6.0', 'l' ) ) {
	// For Joomla 1.5
	class JElementNN_CategoriesFC extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'CategoriesFC';

		function fetchElement( $name, $value, &$node, $control_name )
		{
			$this->_nnfield = new nnFieldCategoriesFC();
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_CategoriesFC extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'CategoriesFC';

		protected function getInput()
		{
			$this->_nnfield = new nnFieldCategoriesFC();
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}