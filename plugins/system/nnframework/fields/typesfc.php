<?php
/**
 * Element: TypesFC
 * Displays a multiselectbox of available Flexicontent Types
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
 * TypesFC Element
 */
class nnFieldTypesFC
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

		if ( !is_array( $value ) ) {
			$value = explode( ',', $value );
		}

		$sql = 'SELECT  id, name FROM #__flexicontent_types WHERE published = 1';
		$db->setQuery( $sql );
		$list = $db->loadObjectList();

		// assemble items to the array
		$options = array();
		foreach ( $list as $item ) {
			$item_name = preg_replace( '#^((&nbsp;)*)- #', '\1', str_replace( '&#160;', '&nbsp;', $item->name ) );
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
	class JElementNN_TypesFC extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'TypesFC';

		function fetchElement( $name, $value, &$node, $control_name )
		{
			$this->_nnfield = new nnFieldTypesFC();
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_TypesFC extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'TypesFC';

		protected function getInput()
		{
			$this->_nnfield = new nnFieldTypesFC();
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}