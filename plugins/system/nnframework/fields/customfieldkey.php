<?php
/**
 * Element: Custom Field Key
 * Displays a custom key field (use in combination with customfieldvalue)
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
class nnFieldCustomFieldKey
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		$this->params = $params;

		$size = ( $this->def( 'size' ) ? 'size="'.$this->def( 'size' ).'"' : '' );
		$class = ( $this->def( 'class' ) ? 'class="'.$this->def( 'class' ).'"' : 'class="text_area"' );
		$value = htmlspecialchars( html_entity_decode( $value, ENT_QUOTES ), ENT_QUOTES );

		JHtml::_( 'behavior.mootools' );
		$document = JFactory::getDocument();
		$document->addScript( JURI::root( true ).'/plugins/system/nnframework/js/script.js?v='.$this->_version );

		$html = '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" '.$class.' '.$size.' />';
		if ( $j15 ) {
			$val_id = str_replace( '_key', '_value', $id );
			$script = "
				window.addEvent( 'domready', function() {
					if ( $( 'span_".$val_id."' ) ) {
						//$( 'span_".$id."' ).injectInside( $( 'span_".$val_id."' ) );
					}
				});
			";
			$document->addScriptDeclaration( $script );
			$html = '<span id="span_'.$id.'">'.$html.'</span>';
			$random = rand( 100000, 999999 );
			$html .= '<div id="end-'.$random.'"></div><script type="text/javascript">NNFrameworkHideTD( "end-'.$random.'" );</script>';
		} else {
			$html = '<label for="'.$id.'" style="margin: 0;">'.$html.'</label>';
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
	class JElementNN_CustomFieldKey extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'CustomFieldKey';

		function fetchTooltip( $label, $description, &$node, $control_name, $name )
		{
			return;
		}

		function fetchElement( $name, $value, &$node, $control_name )
		{
			$this->_nnfield = new nnFieldCustomFieldKey();
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_CustomFieldKey extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'CustomFieldKey';

		protected function getLabel()
		{
			return;
		}

		protected function getInput()
		{
			$this->_nnfield = new nnFieldCustomFieldKey();
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}