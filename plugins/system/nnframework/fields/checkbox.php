<?php
/**
 * Element: Checkbox
 * Displays options as checkboxes
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
 * Checkbox Element
 */
class nnFieldCheckbox
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		$this->params = $params;

		$newlines = $this->def( 'newlines', 0 );
		$showcheckall = $this->def( 'showcheckall', 0 );

		$checkall = ( $value == '*' );

		if ( !$checkall ) {
			if ( !is_array( $value ) ) {
				$value = explode( ',', $value );
			}
		}

		$options = array();
		foreach ( $children as $option ) {
			$text = $option->data();
			$hasval = 0;
			if ( !$j15 && isset( $option['value'] ) ) {
				$val = (string) $option['value'];
				$disabled = (int) $option['disabled'];
				$hasval = 1;
			} else if ( $j15 && isset( $option->_attributes['value'] ) ) {
				$val = $option->attributes( 'value' );
				$disabled = $option->attributes( 'disabled' );
				$hasval = 1;
			}
			if ( $hasval ) {
				$option = '<input type="checkbox" class="nn_'.$id.'" id="'.$id.$val.'" name="'.$name.'[]" value="'.$val.'"';
				if ( $checkall || in_array( $val, $value ) ) {
					$option .= ' checked="checked"';
				}
				if ( $disabled ) {
					$option .= ' disabled="disabled"';
				}
				$option .= ' /> <label for="'.$id.$val.'" class="checkboxes">'.JText::_( $text ).'</label>';
			} else {
				$option = '<label style="clear:both;"><strong>'.JText::_( $text ).'</strong></label>';
			}
			$options[] = $option;
		}

		if ( !$j15 ) {
			$options = implode( '', $options );
		} else if ( $newlines ) {
			$options = implode( '<br />', $options );
		} else {
			$options = implode( '&nbsp;&nbsp;&nbsp;', $options );
		}

		if ( $showcheckall ) {
			$checkers = array();
			if ( $showcheckall ) {
				$checkers[] = '<input id="nn_checkall_'.$id.'" type="checkbox" onclick="NNFrameworkCheckAll( this, \'nn_'.$id.'\' );" /> '.JText::_( ( $j15 ? 'All' : 'JALL' ) );

				$document = JFactory::getDocument();
				$js = "
					window.addEvent('domready', function() {
						$('nn_checkall_".$id."').checked = NNFrameworkAllChecked( 'nn_".$id."' );
					});
				";
				$document->addScriptDeclaration( $js );
			}
			$options = implode( '&nbsp;&nbsp;&nbsp;', $checkers ).'<br />'.$options;
		}
		$options .= '<input type="hidden" id="'.$id.'x" name="'.$name.''.'[]" value="x" checked="checked" />';

		if ( $j15 ) {
			return $options;
		}

		$html = array();
		$html[] = '<fieldset id="'.$id.'" class="'.( $newlines ? 'checkboxes' : 'radio' ).'">';
		$html[] = $options;
		$html[] = '</fieldset>';
		return implode( '', $html );
	}

	private function def( $val, $default = '' )
	{
		return ( isset( $this->params[$val] ) && (string) $this->params[$val] != '' ) ? (string) $this->params[$val] : $default;
	}
}

if ( version_compare( JVERSION, '1.6.0', 'l' ) ) {
	// For Joomla 1.5
	class JElementNN_Checkbox extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'Checkbox';

		function fetchElement( $name, $value, &$node, $control_name )
		{
			$this->_nnfield = new nnFieldCheckbox();
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_Checkbox extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'Checkbox';

		protected function getInput()
		{
			$this->_nnfield = new nnFieldCheckbox();
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}