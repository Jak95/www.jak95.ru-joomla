<?php
/**
 * Element: DateTime
 * Element to display the date and time
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
 * DateTime Element
 */
class nnFieldDateTime
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		$this->params = $params;

		$label = $this->def( 'label' );
		$format = $this->def( 'format' );

		$config = JFactory::getConfig();
		$date = JFactory::getDate();
		if ( $j15 ) {
			$date->setOffset( $config->getValue( 'config.offset' ) );
		} else {
			$date->setTimeZone( new DateTimeZone( $config->getValue( 'config.offset' ) ) );
		}

		if ( $format ) {
			$html = $date->toFormat( $format, 1 );
		} else {
			$html = $date->toFormat( '', 1 );
		}

		if ( $label ) {
			$html = JText::sprintf( $label, $html );
		}

		return $html;
	}

	private function def( $val, $default = '' )
	{
		return ( isset( $this->params[$val] ) && (string) $this->params[$val] != '' ) ? (string) $this->params[$val] : $default;
	}
}

if ( version_compare( JVERSION, '1.6.0', 'l' ) ) {
	// For Joomla 1.5
	class JElementNN_DateTime extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'DateTime';

		function fetchTooltip( $label, $description, &$node, $control_name, $name )
		{
			return;
		}

		function fetchElement( $name, $value, &$node, $control_name )
		{
			$this->_nnfield = new nnFieldDateTime();
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_DateTime extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'DateTime';

		protected function getLabel()
		{
			return;
		}

		protected function getInput()
		{
			$this->_nnfield = new nnFieldDateTime();
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}