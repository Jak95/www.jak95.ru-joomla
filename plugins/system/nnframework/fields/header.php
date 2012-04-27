<?php
/**
 * Element: Header
 * Displays a title with a bunch of extras, like: description, image, versioncheck
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

// Load common functions
require_once JPATH_PLUGINS.'/system/nnframework/helpers/functions.php';

/**
 * Header Element
 *
 * Available extra parameters:
 * title			The title
 * description		The description
 * xml				The xml file for grabbing data
 * language_file	Main name part of the language php file
 * image			Image (and path) to show on the right
 * image_w			Image width
 * image_h			Image height
 * url				The main url
 * help_url			The url of the help page
 */
class nnFieldHeader
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		$this->params = $params;

		$document = JFactory::getDocument();
		$document->addStyleSheet( JURI::root( true ).'/plugins/system/nnframework/css/style.css?v='.$this->_version );

		$title = $this->def( 'label' );
		$description = $this->def( 'description' );
		$xml = $this->def( 'xml' );
		$lang_file = $this->def( 'language_file' );
		$image = $this->def( 'image' );
		$image_w = $this->def( 'image_w' );
		$image_h = $this->def( 'image_h' );
		$url = $this->def( 'url' );
		$help_url = $this->def( 'help_url' );

		if ( $description ) {
			// variables
			$v1 = $this->def( 'var1' );
			$v2 = $this->def( 'var2' );
			$v3 = $this->def( 'var3' );
			$v4 = $this->def( 'var4' );
			$v5 = $this->def( 'var5' );

			$description = NNFrameworkFunctions::html_entity_decoder( trim( JText::sprintf( $description, $v1, $v2, $v3, $v4, $v5 ) ) );
		}

		if ( $lang_file ) {
			jimport( 'joomla.filesystem.file' );

			// Include extra language file
			$language = JFactory::getLanguage();
			$lang = str_replace( '_', '-', $language->getTag() );

			$inc = '';
			$lang_path = 'language/'.$lang.'/'.$lang.'.'.$lang_file.'.inc.php';
			if ( JFile::exists( JPATH_ADMINISTRATOR.'/'.$lang_path ) ) {
				$inc = JPATH_ADMINISTRATOR.'/'.$lang_path;
			} else if ( JFile::exists( JPATH_SITE.'/'.$lang_path ) ) {
				$inc = JPATH_SITE.'/'.$lang_path;
			}
			if ( !$inc && $lang != 'en-GB' ) {
				$lang = 'en-GB';
				$lang_path = 'language/'.$lang.'/'.$lang.'.'.$lang_file.'.inc.php';
				if ( JFile::exists( JPATH_ADMINISTRATOR.'/'.$lang_path ) ) {
					$inc = JPATH_ADMINISTRATOR.'/'.$lang_path;
				} else if ( JFile::exists( JPATH_SITE.'/'.$lang_path ) ) {
					$inc = JPATH_SITE.'/'.$lang_path;
				}
			}
			if ( $inc ) {
				include $inc;
			}
		}

		if ( $title ) {
			$title = JText::_( $title );
		}

		if ( $description ) {
			$description = str_replace( 'span style="font-family:monospace;"', 'span class="nn_code"', $description );
			if ( $description['0'] != '<' ) {
				$description = '<p>'.$description.'</p>';
			}
		}

		if ( $xml ) {
			$xml = JApplicationHelper::parseXMLInstallFile( JPATH_SITE.'/'.$xml );
			$version = 0;
			if ( $xml && isset( $xml['version'] ) ) {
				$version = $xml['version'];
			}
			if ( $version ) {
				if ( $title ) {
					$title .= ' v'.$version;
				} else {
					$title = JText::_( 'Version' ).' '.$version;
				}
			}
		}

		if ( $url ) {
			$url = '<a href="'.$url.'" target="_blank" title="'.$title.'">';
		}

		$html = array();

		$html[] = '<div class="panel nn_panel'.( $j15 ? ' nn_panel_15' : '' ).'"><div class="nn_block nn_title">';

		if ( $image ) {
			$image = str_replace( '/', "\n", str_replace( '\\', '/', $image ) );
			$image = explode( "\n", trim( $image ) );
			if ( $image['0'] == 'administrator' ) {
				$image['0'] = JURI::base( true );
			} else {
				$image['0'] = JURI::root( true ).'/'.$image['0'];
			}
			$image = '<img src="'.implode( '/', $image ).'" border="0" style="float:right;margin-left:10px" alt=""';
			if ( $image_w ) {
				$image .= ' width="'.$image_w.'"';
			}
			if ( $image_h ) {
				$image .= ' height="'.$image_h.'"';
			}
			$image .= ' />';
			if ( $url ) {
				$image = $url.$image.'</a>';
			}
			$html[] = $image;
		}

		if ( $title ) {
			if ( $url ) {
				$title = $url.$title.'</a>';
			}
			$html[] = '<h4 style="margin: 0px;">'.NNFrameworkFunctions::html_entity_decoder( $title ).'</h4>';
		}
		if ( $description ) {
			$html[] = $description;
		}
		if ( $help_url ) {
			$html[] = '<p><a href="'.$help_url.'" target="_blank" title="'.JText::_( 'NN_MORE_INFO' ).'">'.JText::_( 'NN_MORE_INFO' ).'...</a></p>';
		}

		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div></div>';

		return implode( '', $html );
	}

	private function def( $val, $default = '' )
	{
		return ( isset( $this->params[$val] ) && (string) $this->params[$val] != '' ) ? (string) $this->params[$val] : $default;
	}
}

if ( version_compare( JVERSION, '1.6.0', 'l' ) ) {
	// For Joomla 1.5
	class JElementNN_Header extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'Header';

		function fetchTooltip( $label, $description, &$node, $control_name, $name )
		{
			$this->_nnfield = new nnFieldHeader();
			return;
		}

		function fetchElement( $name, $value, &$node, $control_name )
		{
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_Header extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'Header';

		protected function getLabel()
		{
			$this->_nnfield = new nnFieldHeader();
			return;
		}

		protected function getInput()
		{
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}