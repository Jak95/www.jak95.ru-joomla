<?php
/**
 * Element: Browsers
 * Displays a multiselectbox of different browsers
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
 * Browsers Element
 */
class nnFieldBrowsers
{
	var $_version = '12.1.6';

	function getInput( $name, $id, $value, $params, $children, $j15 = 0 )
	{
		$this->params = $params;

		$size = (int) $this->def( 'size' );

		if ( !is_array( $value ) ) {
			$value = explode( ',', $value );
		}

		$browsers = array(
			'Chrome'                   => 'Chrome',
			'- Chrome 1'               => 'Chrome/1.',
			'- Chrome 2'               => 'Chrome/2.',
			'- Chrome 3'               => 'Chrome/3.',
			'- Chrome 4'               => 'Chrome/4.',
			'- Chrome 5'               => 'Chrome/5.',
			'- Chrome 6'               => 'Chrome/6.',
			'- Chrome 8'               => 'Chrome/8.',
			'- Chrome 9'               => 'Chrome/9.',
			'- Chrome 10'              => 'Chrome/10.',
			'- Chrome 11'              => 'Chrome/11.',
			'- Chrome 12'              => 'Chrome/12.',
			'- Chrome 13'              => 'Chrome/13.',
			'@1'                       => '',
			'Firefox'                  => 'Firefox',
			'- Firefox 2.0'            => 'Firefox/2.0',
			'- Firefox 3.0'            => 'Firefox/3.0',
			'- Firefox 3.5'            => 'Firefox/3.5',
			'- Firefox 3.6'            => 'Firefox/3.6',
			'- Firefox 4.0'            => 'Firefox/4.0',
			'- Firefox 5.0'            => 'Firefox/5.0',
			'@2'                       => '',
			'Internet Explorer'		   => 'MSIE',
			'- Internet Explorer 6'	   => 'MSIE 6',
			'- Internet Explorer 7'	   => 'MSIE 7',
			'- Internet Explorer 8'	   => 'MSIE 8',
			'- Internet Explorer 9'	   => 'MSIE 9',
			'@4'                       => '',
			'Opera'                    => 'Opera',
			'- Opera 8.0'              => 'Opera/8.0',
			'- Opera 9.0'              => 'Opera/9.0',
			'- Opera 9.5'              => 'Opera/9.5',
			'- Opera 10.0'             => 'Opera/10.0',
			'- Opera 10.5'             => 'Opera/10.5',
			'- Opera 11.0'             => 'Opera/11.0',
			'- Opera 11.5'             => 'Opera/11.5',
			'@5'                       => '',
			'Safari'                   => 'Safari',
			'- Safari 2'               => 'Safari/41',
			'- Safari 3'               => 'Safari/52',
			'- Safari 4'               => 'Safari/531',
			'- Safari 5'               => 'Safari/533',
			'@6'                       => '',
			'Others'                   => '',
			'Amaya'                    => 'amaya',
			'- Amaya 9.0'              => 'amaya/9.0',
			'- Amaya 10.0'             => 'amaya/10.0',
			'- Amaya 11.0'             => 'amaya/11.0',
			'AOL Explorer'             => 'AOL Explorer',
			'Avant'                    => 'Avant',
			'Camino'                   => 'Camino',
			'- Camino 1'               => 'Camino/1',
			'- Camino 2'               => 'Camino/2',
			'Epiphany'                 => 'Epiphany',
			'Flock'                    => 'Flock',
			'- Flock 1'                => 'Flock/1',
			'- Flock 2'                => 'Flock/2',
			'Konqueror'                => 'Konqueror',
			'Netscape'                 => 'Netscape',
			'- Netscape 8'             => 'Netscape/8',
			'- Netscape 9'             => 'Netscape/9',
			'SeaMonkey'                => 'SeaMonkey',
			'- SeaMonkey 1'            => 'SeaMonkey/1',
			'- SeaMonkey 2'            => 'SeaMonkey/2',
			'Shiira'                   => 'Shiira',
			'@7'                       => '',
			'Web crawlers'             => '',
			'- Alexa'                  => 'ia_archiver-web.archive.org',
			'- Bing'                   => 'bingbot',
			'- Google'                 => 'GoogleBot',
			'- Yahoo'                  => 'Yahoo! Slurp',
		);

		$options = array();
		foreach ( $browsers as $key => $val ) {
			if ( substr( $key, 0, 1 ) == '@' ) {
				$options[] = JHtml::_( 'select.option', '-', '&nbsp;', 'value', 'text', true );
			} else if ( !$val ) {
				$options[] = JHtml::_( 'select.option', '-', $key, 'value', 'text', true );
			} else {
				$item_name = str_replace( '- ', '&nbsp;&nbsp;', $key );
				$options[] = JHtml::_( 'select.option', $val, $item_name, 'value', 'text' );
			}
		}

		require_once JPATH_PLUGINS.'/system/nnframework/helpers/html.php';
		return nnHTML::selectlist( $options, $name, $value, $id, $size, 1, '', $j15 );
	}

	private function def( $val, $default = '' )
	{
		return ( isset( $this->params[$val] ) && (string) $this->params[$val] != '' ) ? (string) $this->params[$val] : $default;
	}
}

if ( version_compare( JVERSION, '1.6.0', 'l' ) ) {
	// For Joomla 1.5
	class JElementNN_Browsers extends JElement
	{
		/**
		 * Element name
		 *
		 * @access	protected
		 * @var		string
		 */
		var $_name = 'Browsers';

		function fetchElement( $name, $value, &$node, $control_name )
		{
			$this->_nnfield = new nnFieldBrowsers();
			return $this->_nnfield->getInput( $control_name.'['.$name.']', $control_name.$name, $value, $node->attributes(), $node->children(), 1 );
		}
	}
} else {
	// For Joomla 1.6
	class JFormFieldNN_Browsers extends JFormField
	{
		/**
		 * The form field type
		 *
		 * @var		string
		 */
		public $type = 'Browsers';

		protected function getInput()
		{
			$this->_nnfield = new nnFieldBrowsers();
			return $this->_nnfield->getInput( $this->name, $this->id, $this->value, $this->element->attributes(), $this->element->children() );
		}
	}
}