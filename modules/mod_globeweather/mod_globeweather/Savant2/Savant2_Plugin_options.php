<?php	// modified by RJA for Joomla! compatibility

/**
* Base plugin class.
*/

require_once JPATH_SITE.'/modules/mod_globeweather/mod_globeweather/Savant2/Plugin.php';

/**
* 
* Outputs a series of HTML <option>s.
* 
* $Id: Savant2_Plugin_options.php,v 1.1.1.1 2004/07/20 13:11:54 vasco Exp $
* 
* @author Paul M. Jones <pmjones@ciaweb.net>
* 
* @package Savant2
* 
* @license http://www.gnu.org/copyleft/lesser.html LGPL
* 
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as
* published by the Free Software Foundation; either version 2.1 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, but
* WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
* 
*/

class Savant2_Plugin_options extends Savant2_Plugin {

	/**
	*
	* Outputs a series of HTML <option>s.
	* 
	* Outputs a series of HTML <option>s based on an associative array
	* where the key is the option value and the value is the option
	* label. You can pass a "selected" value as well to tell the
	* function which option value(s) should be marked as seleted.
	* 
	* @access public
	* 
	* @param array $options An associative array of key-value pairs; the
	* key is the option value, the value is the option label.
	* 
	* @param string|array $selected A string or array that matches one
	* or more option values, to tell the function what options should be
	* marked as selected.  Defaults to an empty array.
	* 
	* @param bool $dual If true, the $options array values are used as
	* both the option value and the option label.  If false (the
	* default) then the $options array key is used as the option value
	* and the $options array value is used as the option label.
	* 
	* @param string|array $attr Extra attributes to apply to the option
	* tag.  If a string, they are added as-is; if an array, the key is
	* the attribute name and the value is the attribute value.
	* 
	* @return string A set of HTML <option> tags.
	* 
	*/
	
	function plugin($options, $selected = array(), $dual = false, $attr = null)
	{
		$html = '';
		
		// force $selected to be an array.  this allows multi-selects to
		// have multiple selected options.
		settype($selected, 'array');
		settype($options, 'array');
		
		// loop through the options array
		foreach ($options as $value => $label) {
			
			// if treating the array as sequential, the option value
			// is the same as the option label.
			if ($dual) {
				$value = $label;
			}
			
			// escape HTML
			// set the value and label in the tag
			$html .= '<option value="' . htmlspecialchars($value) . '"';
			$html .= ' label="' . htmlspecialchars($label) . '"';
			
			// is the option one of the selected values?
			if (in_array($value, $selected)) {
				$html .= ' selected="selected"';
			}
			
			// are we adding extra attributes?
			if (is_array($attr)) {
				// yes, from an array
				foreach ($attr as $key => $val) {
					$val = htmlspecialchars($val);
					$html .= " $key=\"$val\"";
				}
			} elseif (! is_null($attr)) {
				// yes, from a string
				$html .= ' ' . $attr;
			}
			
			// add the label and close the tag
			$html .= '>' . htmlspecialchars($label) . "</option>\n";
		}
		
		return $html;
	}
}
?>