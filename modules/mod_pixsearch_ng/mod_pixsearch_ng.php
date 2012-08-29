<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once ( dirname(__FILE__).DS.'helper.php' );

modPixsearchHelperNG::init( $params );

require( JModuleHelper::getLayoutPath( 'mod_pixsearch_ng' ) );