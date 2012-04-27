<?php
/**
 * Element: Radio List
 * Displays a list of radio items with a break after each item
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
 * Radio List Element
 */
class nnFieldRadioList
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		$this->params = $params;

		if ( $j15 ) {
			$options = array();
			foreach ( $children as $option ) {
				$val = $option->attributes( 'value' );
				$text = $option->data();
				$options[] = JHtml::_( 'select.option', $val, JText::_( $text ).'<br />' );
			}

			return JHtml::_( 'select.radiolist', $options, ''.$name.'', '', 'value', 'text', $value, $id );
		} else {
			$html = array();

			$html[] = '<fieldset id="'.$id.'" class="radio">';

			$options = array();
			foreach ( $children as $i => $option ) {
				$checked = ( (string) $option['value'] == (string) $value ) ? ' checked="checked"' : '';
				$text = trim( (string) $option );
				$html[] = '<input type="radio" id="'.$id.$i.'" name="'.$name.'"'.
					' value="'.htmlspecialchars( (string) $option['value'], ENT_COMPAT, 'UTF-8' ).'"'
					.$checked.' class="radio" style="clear:left;" />';

				$html[] = '<label for="'.$id.$i.'" class="radio" style="width:auto;min-width:none;">'.JText::_( $text ).'</label>';
			}

			$html[] = '</fieldset>';

			return implode( '', $html );
		}
	}

	private function def( $val, $default = '' )
	{
		return ( isset( $this->params[$val] ) && (string) $this->params[$val] != '' ) ? (string) $this->params[$val] : $default;
	}
}

if ( version_compare( JVERSION, '1.6.0', 'l' ) ) {
	// For Joomla 1.5
	class JElementNN_RadioList extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'RadioList';

		function fetchElement( $name, $value, &$node, $control_name )
		{
			$this->_nnfield = new nnFieldRadioList();
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_RadioList extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'RadioList';

		protected function getInput()
		{
			$this->_nnfield = new nnFieldRadioList();
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}