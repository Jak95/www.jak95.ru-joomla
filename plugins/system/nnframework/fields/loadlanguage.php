<?php
/**
 * Element: Load Language
 * Loads the English language file as fallback
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
 * Load Language Element
 */
class nnFieldLoadLanguage
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		$this->params = $params;

		JHtml::_( 'behavior.mootools' );
		$document = JFactory::getDocument();
		$document->addScript( JURI::root( true ).'/plugins/system/nnframework/js/script.js?v='.$this->_version );

		$extension = $this->def( 'extension' );
		$admin = $this->def( 'admin', 1 );

		$path = $admin ? JPATH_ADMINISTRATOR : JPATH_SITE;
		// load the admin language file
		$lang = JFactory::getLanguage();
		if ( $lang->getTag() != 'en-GB' ) {
			// Loads English language file as fallback (for undefined stuff in other language file)
			$lang->load( $extension, $path, 'en-GB' );
		}
		$lang->load( $extension, $path, null, 1 );

		if ( $j15 ) {
			$random = rand( 100000, 999999 );
			return '<div id="end-'.$random.'"></div><script type="text/javascript">NNFrameworkHideTD( "end-'.$random.'" );</script>';
		} else {
			return;
		}
	}

	function loadLanguage( $extension, $admin = 1 )
	{
		if ( $extension ) {
			if ( $admin ) {
				$path = JPATH_ADMINISTRATOR;
			} else {
				$path = JPATH_SITE;
			}
			$lang = JFactory::getLanguage();
			if ( $lang->getTag() != 'en-GB' ) {
				// Loads English language file as fallback (for undefined stuff in other language file)
				$lang->load( $extension, $path, 'en-GB' );
			}
			$lang->load( $extension, $path, null, 1 );
		}
	}

	private function def( $val, $default = '' )
	{
		return ( isset( $this->params[$val] ) && (string) $this->params[$val] != '' ) ? (string) $this->params[$val] : $default;
	}
}

if ( version_compare( JVERSION, '1.6.0', 'l' ) ) {
	// For Joomla 1.5
	class JElementNN_LoadLanguage extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'LoadLanguage';

		function fetchTooltip( $label, $description, &$node, $control_name, $name )
		{
			return;
		}

		function fetchElement( $name, $value, &$node, $control_name )
		{
			$this->_nnfield = new nnFieldLoadLanguage();
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_LoadLanguage extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'LoadLanguage';

		protected function getLabel()
		{
			return;
		}

		protected function getInput()
		{
			$this->_nnfield = new nnFieldLoadLanguage();
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}