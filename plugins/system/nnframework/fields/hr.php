<?php
/**
 * Element: HR
 * Displays a line
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
 * HR Element
 */
class nnFieldHR
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet( JURI::root( true ).'/plugins/system/nnframework/css/style.css?v='.$this->_version );

		return '<div class="panel nn_panel nn_hr'.( $j15 ? ' nn_panel_15' : '' ).'"></div>';
	}

	private function def( $val, $default = '' )
	{
		return ( isset( $this->params[$val] ) && (string) $this->params[$val] != '' ) ? (string) $this->params[$val] : $default;
	}
}

if ( version_compare( JVERSION, '1.6.0', 'l' ) ) {
	// For Joomla 1.5
	class JElementNN_HR extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'HR';

		function fetchTooltip( $label, $description, &$node, $control_name, $name )
		{
			$this->_nnfield = new nnFieldHR();
			return;
		}

		function fetchElement( $name, $value, &$node, $control_name )
		{
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_HR extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'HR';

		protected function getLabel()
		{
			$this->_nnfield = new nnFieldHR();
			return;
		}

		protected function getInput()
		{
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}