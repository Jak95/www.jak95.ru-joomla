<?php
/**
 * 			plg System RSD
 * @version	 	1.1.0
 * @package		XMLRPC
 * @copyright		Copyright (C) 2007 - 2011 Joomler!.net. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @author		Joomler!.net  joomlers@gmail.com
 * @link 			http://www.joomler.net/
 */

/**
* @package		Joomla
* @copyright		Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );

class  plgSystemRSD extends JPlugin
{

	public function onAfterRoute()
	{
		$app = JFactory::getApplication();

		if($app->isAdmin()){
			return;
		}

		$menus = $app->getMenu('site');
		$menu = $menus->getActive();
		if(!$menu || $menu->home != 1) return;

		$doc = JFactory::getDocument();
		if($doc->getType() != 'html') return;

		$params = JComponentHelper::getParams('com_xmlrpc');

		if($params->get('show_rsd', 1)){
			class_exists('XMLRPCHelperRoute') or require(JPATH_SITE.'/components/com_xmlrpc/helpers/route.php');
			$link = JRoute::_(XMLRPCHelperRoute::getRsdRoute());
			$doc->addHeadLink($link, 'EditURI', 'rel', array('type' => 'application/rsd+xml', 'title'=>'RSD'));
		}

		if($params->get('show_manifest', 1)){
			class_exists('XMLRPCHelperRoute') or require(JPATH_SITE.'/components/com_xmlrpc/helpers/route.php');
			$link = JRoute::_(XMLRPCHelperRoute::getManifestRoute());
			$doc->addHeadLink($link, 'wlwmanifest', 'rel', array('type'=>'application/wlwmanifest+xml'));
		}
	}
}