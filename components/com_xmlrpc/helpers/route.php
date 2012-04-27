<?php
/**
 * 			XMLRPC Helper Route
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

/**
 * XMLRPC Component Route Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	XMLRPC
 * @since 1.5
 */
abstract class XMLRPCHelperRoute
{
	public static function getRsdRoute()
	{
		$link = 'index.php?option=com_xmlrpc&view=rsd&format=xml';

		return $link;
	}

	public static function getManifestRoute()
	{
		$link = 'index.php?option=com_xmlrpc&view=manifest&format=xml';

		return $link;
	}

	public static function getServiceRoute()
	{
		$link = 'index.php?option=com_xmlrpc';

		return $link;
	}
}