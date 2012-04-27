<?php
/**
 * 			XMLRPC View Configuration
 * @version		1.0.0
 * @package		XMLRPC for Joomla!
 * @copyright		Copyright (C) 2007-2011 Joomler!.net. All rights reserved.
 * @license		GNU/GPL 2.0 or higher
 * @author		Joomler!.net  joomlers@gmail.com
 * @link			http://www.joomler.net
 */

/**
* @package		Joomla
* @copyright		Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class XMLRPCViewConfiguration extends JView
{
	public function display($tpl = null)
	{
		$style = '.icon-48-xmlrpc{background:url(components/com_xmlrpc/assets/images/xmlrpc48.png)}';
		JFactory::getDocument()->addStyleDeclaration($style);

		$xmlrpc_plugins = JPluginHelper::getPlugin('xmlrpc');
		$rsd_plugins = JPluginHelper::getPlugin('system', 'rsd');

		$this->assign('xmlrpc_plugins', $xmlrpc_plugins);
		$this->assign('rsd_plugins', $rsd_plugins);

		JToolBarHelper::title(JText::_('COM_XMLRPC_TITLE'), 'xmlrpc.png');
		JToolBarHelper::preferences('com_xmlrpc');
		parent::display($tpl);
	}
}