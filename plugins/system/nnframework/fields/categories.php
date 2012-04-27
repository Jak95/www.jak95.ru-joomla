<?php
/**
 * Element: Categories
 * Displays a selectbox of available categories (needs sections element)
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
 * Categories Element
 */
class nnFieldCategories
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		$this->params = $params;

		global $CT_filter_sectionid;

		$db = JFactory::getDBO();

		$count = 0;
		$script = '<script language="javascript" type="text/javascript">'."\n";
		$script .= 'var sectioncategories = new Array;'."\n";
		$script .= 'sectioncategories['.$count++.'] = new Array( "-1","-1","- '.JText::_( 'Select section' ).' -" );'."\n";
		$script .= 'sectioncategories['.$count++.'] = new Array( "0","0","'.JText::_( 'Uncategorized' ).'" );'."\n";

		$query = 'SELECT id, title FROM #__sections WHERE published = 1 AND scope = "content" ORDER BY ordering';
		$db->setQuery( $query );
		$sections = $db->loadObjectList();
		$sec_count = count( $sections );
		for ( $i = 0; $i < $sec_count; $i++ ) {
			$query = 'SELECT c.id, c.title'
				.' FROM #__categories AS c'
				.' LEFT JOIN #__sections AS s'
				.' ON s.id = c.section'
				.' WHERE c.published = 1'
				.' AND s.id = '.$sections[$i]->id
				.' ORDER BY c.ordering';
			$db->setQuery( $query );
			$categories = $db->loadObjectList();
			$cat_count = count( $categories );
			if ( $cat_count > 1 ) {
				$script .= 'sectioncategories['.$count++.'] = new Array( "'.$sections[$i]->id.'","-1","- '.JText::_( 'Select category' ).' -" );'."\n";
			}
			for ( $j = 0; $j < $cat_count; $j++ ) {
				$title = explode( "\n", wordwrap( $categories[$j]->title, 86, "\n" ) );
				$title = $title['0'];
				$title = ( $title != $categories[$j]->title ) ? $title.'...' : $title;
				$script .= 'sectioncategories['.$count++.'] = new Array( "'.$sections[$i]->id.'","'.$categories[$j]->id.'","'.$title.'" );'."\n";
			}
		}
		$script .= '</script>';

		$categories = array();
		if ( $CT_filter_sectionid >= 0 ) {
			$query = 'SELECT cc.id AS value, cc.title AS text, section'
				.' FROM #__categories AS cc'
				.' INNER JOIN #__sections AS s ON s.id = cc.section'
				.' WHERE cc.section = '.$db->quote( $CT_filter_sectionid )
				.' ORDER BY s.ordering, cc.ordering';
			$db->setQuery( $query );
			$cats = $db->loadObjectList();
			if ( count( $cats ) > 1 ) {
				$categories[] = JHtml::_( 'select.option', '-1', '- '.JText::_( 'Select category' ).' -' );
			}
			$categories = array_merge( $categories, $cats );
		} else {
			$categories[] = JHtml::_( 'select.option', '-1', '- '.JText::_( 'Select section' ).' -' );
		}
		return $script.JHtml::_( 'select.genericlist', $categories, $name.'[]', 'class="inputbox" size="1"', 'value', 'text', $value, $id );
	}

	private function def( $val, $default = '' )
	{
		return ( isset( $this->params[$val] ) && (string) $this->params[$val] != '' ) ? (string) $this->params[$val] : $default;
	}
}

if ( version_compare( JVERSION, '1.6.0', 'l' ) ) {
	// For Joomla 1.5
	class JElementNN_Categories extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'Categories';

		function fetchElement( $name, $value, &$node, $control_name )
		{
			$this->_nnfield = new nnFieldCategories();
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_Categories extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'Categories';

		protected function getInput()
		{
			$this->_nnfield = new nnFieldCategories();
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}