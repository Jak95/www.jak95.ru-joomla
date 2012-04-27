<?php
/**
 * Mobile Joomla!
 * http://www.mobilejoomla.com
 *
 * @version		1.0.3
 * @license		GNU/GPL v2 - http://www.gnu.org/licenses/gpl-2.0.html
 * @copyright	(C) 2008-2012 Kuneri Ltd.
 * @date		April 2012
 */
defined('_JEXEC') or die('Restricted access');

if(!defined('_MJ'))
	return;

require_once(dirname(__FILE__).DS.'helper.php'); 

$MobileJoomla = MobileJoomla::getInstance();

/** @var JParameter $params */
$params->def('menutype', 'mainmenu');
$params->def('layout', '');
$params->def('type', 'submenu');
$params->def('class_sfx', '');
$params->def('excludemenu', '');
$params->def('format', 0);
$params->def('accesskey', 1);

if($params->get('layout')=='')
{
	if($MobileJoomla->getMarkup() == 'iphone')
		$params->set('layout', 'v');
	else
		$params->set('layout', 'h');
}

$app = JFactory::getApplication();
$sitemenu = $app->getMenu();
$active	= $sitemenu->getActive();

if(isset($active) && $active->menutype==$params->get('menutype'))
{
	$rows = (array)JMobileMenuHelper::getSiblings($active);
	$subrows = (array)JMobileMenuHelper::getChildrens($active);
	if($params->get('type')=='submenu')
	{
		if(!$MobileJoomla->isHome())
			$rows = $subrows;
		$subrows = array();
	}
}
else
{
	$rows = (array)JMobileMenuHelper::getRoot($params->get('menutype'));
	$subrows = array();
}

$exclude_menu_ids = explode(',', $params->get('excludemenu'));
JMobileMenuHelper::prepareMenu($rows, $exclude_menu_ids, $params);
JMobileMenuHelper::prepareMenu($subrows, $exclude_menu_ids, $params);

$params->set('has_submenu', count($subrows)?1:0);

if($params->get('layout')=='v')
{
	JMobileMenuHelper::renderMenu($rows, $params, $subrows);
}
else
{
	JMobileMenuHelper::renderMenu($rows, $params);
	if(count($subrows))
		JMobileMenuHelper::renderSubmenu($subrows, $params);
}
