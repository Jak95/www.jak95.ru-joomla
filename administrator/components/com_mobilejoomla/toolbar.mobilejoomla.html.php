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

class TOOLBAR_mobilejoomla
{
	static function _DEFAULT()
	{
		JToolBarHelper::title(JText::_('COM_MJ__MOBILE_JOOMLA_SETTINGS'), 'config.php');
		JToolBarHelper::apply();
		JToolBarHelper::cancel('cancel');
		$version = substr(JVERSION,0,3);
		$user = JFactory::getUser();
		if($version != '1.5' && $user->authorise('core.admin', 'com_mobilejoomla'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_mobilejoomla');
		}
	}
}
