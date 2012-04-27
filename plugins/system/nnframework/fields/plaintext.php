<?php
/**
 * Element: PlainText
 * Displays plain text as element
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
 * PlainText Element
 */
class nnFieldPlainText
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		if ( $j15 ) {
			return JText::_( $value );
		}
		return '<fieldset class="radio"><label>'.JText::_( $value ).'</label></fieldset>';
	}
}

if ( version_compare( JVERSION, '1.6.0', 'l' ) ) {
	// For Joomla 1.5
	class JElementNN_PlainText extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'PlainText';

		function fetchElement( $name, $value, &$node, $control_name )
		{
			$this->_nnfield = new nnFieldPlainText();
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_PlainText extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'PlainText';

		protected function getInput()
		{
			$this->_nnfield = new nnFieldPlainText();
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}