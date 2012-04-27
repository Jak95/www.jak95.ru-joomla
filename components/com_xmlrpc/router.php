<?php
/**
 * 			XMLRPC Router
 * @version			1.0.0
 * @package			XMLRPC for Joomla!
 * @copyright			Copyright (C) 2007-2011 Joomler!.net. All rights reserved.
 * @license			http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @author			Yoshiki Kozaki : joomlers@gmail.com
 * @link			http://www.joomler.net/
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

function XMLRPCBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view'])) {
		$view = $query['view'];

		if (empty($query['Itemid'])) {
			$segments[] = $query['view'];
		}

		unset($query['view']);
	}

	return $segments;
}

function XMLRPCParseRoute($segments)
{
	$vars = array();

	// Count route segments
	$count = count($segments);

	if($count){
		$vars['view'] = $segments[0];
	}

	return $vars;
}