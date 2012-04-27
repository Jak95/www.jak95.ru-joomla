<?php
/**
 * Element: Sections / Categories
 * Displays a (multiple) selectbox of available sections and categories
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
 * Sections / Categories Element
 */
class nnFieldSecsCats
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		$this->params = $params;

		$size = (int) $this->def( 'size' );
		$multiple = $this->def( 'multiple' );
		$show_uncategorized = $this->def( 'show_uncategorized' );
		$auto_select_cats = $this->def( 'auto_select_cats', 1 );

		$db = JFactory::getDBO();

		if ( is_array( $value ) ) {
			$value = implode( ',', $value );
		}
		$value = str_replace( '.', ':', $value );
		$value = explode( ',', $value );

		$query = 'SELECT id, 0 as parent, title as name FROM #__sections WHERE published = 1 AND scope = "content" ORDER BY ordering';
		$db->setQuery( $query );
		$sections = $db->loadObjectList();
		for ( $i = 0; $i < count( $sections ); $i++ ) {
			$sec_name = explode( "\n", wordwrap( $sections[$i]->name, 86, "\n" ) );
			$sec_name = $sec_name['0'];
			$sec_name = ( $sec_name != $sections[$i]->name ) ? $sec_name.'...' : $sec_name;
			$sections[$i]->title = $sec_name;
		}

		$children = array();
		$children[] = $sections;
		foreach ( $sections as $section ) {
			$query = 'SELECT CONCAT( '.$section->id.', ":", id ) as id, section as parent, title as name'
				.' FROM #__categories'
				.' WHERE published = 1'
				.' AND section = '.$section->id
				.' ORDER BY ordering';
			$db->setQuery( $query );
			$categories = $db->loadObjectList();
			for ( $i = 0; $i < count( $categories ); $i++ ) {
				$cat_name = explode( "\n", wordwrap( $categories[$i]->name, 86, "\n" ) );
				$cat_name = $cat_name['0'];
				$cat_name = ( $cat_name != $categories[$i]->name ) ? $cat_name.'...' : $cat_name;
				$categories[$i]->name = $cat_name;
				if ( $auto_select_cats && in_array( $section->id, $value ) ) {
					$value[] = $categories[$i]->id;
				}
			}
			$children[$section->id] = $categories;
		}

		// second pass - get an indent list of the items
		require_once JPATH_LIBRARIES.'/joomla/html/html/menu.php';
		$list = JHTMLMenu::treerecurse( 0, '', array(), $children, 9999, 0, 0 );

		// assemble items to the array
		$options = array();
		if ( $show_uncategorized ) {
			$options[] = JHtml::_( 'select.option', '0', JText::_( 'Uncategorized' ), 'value', 'text', 0 );
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
	class JElementNN_SecsCats extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'SecsCats';

		function fetchElement( $name, $value, &$node, $control_name )
		{
			$this->_nnfield = new nnFieldSecsCats();
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_SecsCats extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'SecsCats';

		protected function getInput()
		{
			$this->_nnfield = new nnFieldSecsCats();
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}