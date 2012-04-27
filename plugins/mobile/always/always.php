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

jimport('joomla.plugin.plugin');

class plgMobileAlways extends JPlugin
{
	function plgMobileAlways(& $subject, $config)
	{
		parent::__construct($subject, $config);
		if(!isset($this->params))
			$this->params = new JParameter(null);
	}

	function onDeviceDetection(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		$markup = $this->params->get('markup', '');
		if($markup)
			$MobileJoomla_Device['markup'] = $markup;
	}
}
