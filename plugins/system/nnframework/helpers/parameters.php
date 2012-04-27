<?php
/**
 * NoNumber! Framework Helper File: Parameters
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
 * Assignments
 * $assignment = no / include / exclude / none
 */

class NNePparameters extends NNParameters
{
	// for backward compatibility
}

class NNParameters
{
	public static $instance = null;

	public static function getInstance()
	{
		if ( !self::$instance ) {
			self::$instance = new NNFrameworkParameters;
		}

		return self::$instance;
	}

	public static function getParameters()
	{
		// backward compatibility
		return self::getInstance();
	}
}

class NNFrameworkParameters
{
	var $_version = '12.1.6';

	var $_xml = array();

	function getParams( $ini, $path = '' )
	{
		$xml = $this->_getXML( $path );

		if ( !$ini ) {
			return (object) $xml;
		}

		if ( !is_object( $ini ) ) {
			$registry = new JRegistry();
			$registry->loadString( $ini );
			$params = $registry->toObject();
		} else {
			$params = $ini;
		}

		if ( !empty( $xml ) ) {
			foreach ( $xml as $key => $val ) {
				if ( !isset( $params->$key ) || $params->$key == '' ) {
					$params->$key = $val;
				}
			}
		}

		return $params;
	}

	function getPluginParamValues( $name, $type = 'system' )
	{
		jimport( 'joomla.plugin.plugin' );
		$plugin = JPluginHelper::getPlugin( $type, $name );
		$registry = new JRegistry();
		$registry->loadString( $plugin->params );
		return $this->getParams( $registry->toObject(), JPATH_PLUGINS.'/'.$type.'/'.$name.'/'.$name.'.xml' );
	}

	function _getXML( $path )
	{
		if ( !isset( $this->_xml[$path] ) ) {
			$this->_xml[$path] = $this->_loadXML( $path );
		}

		return $this->_xml[$path];
	}

	function _loadXML( $path )
	{
		$xml = array();
		if ( $path ) {
			$xmlparser = JFactory::getXMLParser( 'Simple' );
			if ( $xmlparser->loadFile( $path ) ) {
				$xml = $this->_getParamValues( $xmlparser );
			}
		}

		return $xml;
	}

	function _getParamValues( &$xml, $keys = array() )
	{
		$params = array();
		$fieldsets = $this->_getFieldSets( $xml );

		foreach ( $fieldsets as $fieldset ) {
			if ( $fieldset->name() == 'fieldset' ) {
				foreach ( $fieldset->children() as $field ) {
					$key = $field->attributes( 'name' );
					if ( !empty( $key ) && $key['0'] != '@' || $field->attributes( 'type' ) != 'spacer' ) {
						if ( empty( $keys ) || in_array( $key, $keys ) ) {
							$val = $xml->get( $key );
							if ( !is_array( $val ) && !strlen( $val ) ) {
								$val = $field->attributes( 'default' );
								if ( $field->attributes( 'type' ) == 'textarea' ) {
									$val = str_replace( '<br />', "\n", $val );
								}
							}
							$params[$key] = $val;
						}
					}
				}
			}
		}
		return $params;
	}

	function _getFieldSets( &$xml )
	{
		if ( isset( $xml->document ) ) {
			$children = $xml->document->children();
			return $this->_getFieldSets( $children );
		} else if ( is_array( $xml ) && isset( $xml['0'] ) && is_object( $xml['0'] ) ) {
			if ( isset( $xml['0']->_name ) && $xml['0']->_name == 'fieldset' ) {
				return $xml;
			} else if ( isset( $xml['0']->_children ) ) {
				foreach ( $xml as $child ) {
					if ( isset( $child->_name ) && in_array( $child->_name, array( 'config', 'fields' ) ) ) {
						$children = $child->children();
						return $this->_getFieldSets( $children );
					}
				}
			}
		}
		return array();
	}

	function getObjectFromXML( &$xml )
	{
		if ( !is_array( $xml ) ) {
			$xml = array( $xml );
		}
		$class = new stdClass();
		foreach ( $xml as $item ) {
			$key = $this->_getKeyFromXML( $item );
			$val = $this->_getValFromXML( $item );

			if ( isset( $class->$key ) ) {
				if ( !is_array( $class->$key ) ) {
					$class->$key = array( $class->$key );
				}
				$class->{$key}[] = $val;
			}
			$class->$key = $val;
		}
		return $class;
	}

	function _getKeyFromXML( &$xml )
	{
		if ( !empty( $xml->_attributes ) && isset( $xml->_attributes['name'] ) ) {
			$key = $xml->_attributes['name'];
		} else {
			$key = $xml->_name;
		}
		return $key;
	}

	function _getValFromXML( &$xml )
	{
		if ( !empty( $xml->_attributes ) && isset( $xml->_attributes['value'] ) ) {
			$val = $xml->_attributes['value'];
		} else if ( empty( $xml->_children ) ) {
			$val = $xml->_data;
		} else {
			$val = new stdClass();
			foreach ( $xml->_children as $child ) {
				$k = $this->_getKeyFromXML( $child );
				$v = $this->_getValFromXML( $child );

				if ( isset( $val->$k ) ) {
					if ( !is_array( $val->$k ) ) {
						$val->$k = array( $val->$k );
					}
					$val->{$k}[] = $v;
				} else {
					$val->$k = $v;
				}
			}
		}
		return $val;
	}

	function getPluginParams( $plugin, $folder = 'system' )
	{
		static $instance;
		if ( !is_object( $instance ) ) {
			$xmlfile = JPATH_PLUGINS.'/'.$folder.'/'.$plugin.'.xml';
			$plug = JPluginHelper::getPlugin( $folder, $plugin );
			$instance = $this->getParams( $plug->params, $xmlfile );
		}
		return $instance;
	}
}
