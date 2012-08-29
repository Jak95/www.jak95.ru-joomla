<?php
/**
 * Mobile Joomla! ScientiaMobile DBI extension
 * http://www.mobilejoomla.com
 *
 * @version		1.1-2012.03.26
 * @license		AGPL
 * @copyright	(C) 2008-2012 Kuneri Ltd.
 * @date		June 2012
 */
defined('_JEXEC') or die('Restricted access');

class plgMobileScientiaInstallerScript
{
	public function postflight($route, $installer)
	{
		require_once( JPATH_ROOT .DS.'plugins'.DS.'mobile'.DS.'scientia'.DS.'scientia'.DS.'scientia_helper.php' );

		ScientiaHelper::disablePlugin();
		switch($route)
		{
		case 'update':
		case 'install':
			$status = ScientiaHelper::installDatabase();
			if($status)
			{
				ScientiaHelper::disablePlugin('amdd');
				ScientiaHelper::enablePlugin();
			}
			break;
		case 'uninstall':
			ScientiaHelper::disablePlugin();
			ScientiaHelper::enablePlugin('amdd');
			ScientiaHelper::dropDatabase();
			break;
		}
		return true;
	}
}