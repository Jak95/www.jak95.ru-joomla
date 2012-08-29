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

jimport('joomla.plugin.plugin');

class plgMobileScientia extends JPlugin
{
	function plgMobileScientia(& $subject, $config)
	{
		parent::__construct($subject, $config);
		if(!isset($this->params))
			$this->params = new JParameter(null);
	}

	function onDeviceDetection(&$MobileJoomla_Settings, &$MobileJoomla_Device)
	{
		if(version_compare(phpversion(), '5.0.0', '<'))
		{
			/** @var JDatabase $db */
			$db = JFactory::getDBO();
			$query = "UPDATE #__plugins SET published = 0 WHERE element = 'scientia' AND folder = 'mobile'";
			$db->setQuery($query);
			$db->query();
			return;
		}

		if(!isset($_SERVER['HTTP_ACCEPT']) && !isset($_SERVER['HTTP_USER_AGENT']))
			return;

		require_once(dirname(__FILE__).DS.'scientia'.DS.'TeraWurflConfig.php');

		/** @var JRegistry $config */
		$config = JFactory::getConfig();
		$c = (substr(JVERSION,0,3)=='1.5') ? 'config.' : '';
		$host = $config->getValue($c.'host');
		if($host=='' || $host[0]==':')
			$host = 'localhost'.$host;
		TeraWurflConfig::$TABLE_PREFIX = $config->getValue($c.'dbprefix').'TeraWurfl';
		TeraWurflConfig::$DB_HOST      = $host;
		TeraWurflConfig::$DB_USER      = $config->getValue($c.'user');
		TeraWurflConfig::$DB_PASS      = $config->getValue($c.'password');
		TeraWurflConfig::$DB_SCHEMA    = $config->getValue($c.'db');
		TeraWurflConfig::$LOG_LEVEL    = 0;

		$mysql4 = $this->params->get('mysql4', 0);
		if($mysql4)
			TeraWurflConfig::$DB_CONNECTOR = 'MySQL4';
		else
			TeraWurflConfig::$DB_CONNECTOR = 'MySQL5';

		$cache = (bool)$this->params->get('cache', 0);
		TeraWurflConfig::$CACHE_ENABLE = $cache;

		require_once(dirname(__FILE__).DS.'scientia'.DS.'TeraWurfl.php');

		try
		{
			$wurflObj = new TeraWurfl();
			if(!is_object($wurflObj) || $wurflObj->db->connected !== true)
				return;
			$wurflObj->getDeviceCapabilitiesFromAgent();
		}
		catch(exception $e)
		{
			error_log("Caught exception 'Exception' with message '".$e->getMessage()."' in ".$e->getFile().':'.$e->getLine());
			return;
		}

		if($wurflObj->getDeviceCapability('is_tablet'))
		{
			$MobileJoomla_Device['markup'] = 'tablet';
		}
		elseif($wurflObj->getDeviceCapability('is_wireless_device'))
		{
			if($wurflObj->getDeviceCapability('device_os')=='iPhone OS')
			{
				$MobileJoomla_Device['markup'] = 'iphone';
			}
			else switch($wurflObj->getDeviceCapability('preferred_markup'))
			{
				case 'wml_1_1':
				case 'wml_1_2':
				case 'wml_1_3':
					$MobileJoomla_Device['markup'] = 'wml';
					break;
				case 'html_wi_imode_compact_generic':
				case 'html_wi_imode_html_1':
				case 'html_wi_imode_html_2':
				case 'html_wi_imode_html_3':
				case 'docomo_imode_html_3':
				case 'html_wi_imode_html_4':
				case 'html_wi_imode_html_5':
				case 'html_wi_imode_htmlx_1':
				case 'html_wi_imode_htmlx_1_1':
				case 'html_wi_imode_htmlx_2':
				case 'html_wi_imode_htmlx_2_1':
				case 'html_wi_imode_htmlx_2_2':
				case 'html_wi_imode_htmlx_2_3':
					$MobileJoomla_Device['markup'] = 'chtml';
					break;
				case 'html_wi_oma_xhtmlmp_1_0': //application/vnd.wap.xhtml+xml
				case 'html_wi_w3_xhtmlbasic':   //application/xhtml+xml DTD XHTML Basic 1.0
				case 'html_wi_mml_html':
				case 'html_web_3_2': //text/html DTD HTML 3.2 Final
				case 'html_web_4_0': //text/html DTD HTML 4.01 Transitional
				case 'html_web_5_0': //text/html HTML5
					$MobileJoomla_Device['markup'] = 'xhtml';
					break;
			}
			$MobileJoomla_Device['screenwidth']  = $wurflObj->getDeviceCapability('max_image_width');
			$MobileJoomla_Device['screenheight'] = $wurflObj->getDeviceCapability('max_image_height');

			$MobileJoomla_Device['imageformats'] = array ();
			if($wurflObj->getDeviceCapability('png'))
				$MobileJoomla_Device['imageformats'][] = 'png';
			if($wurflObj->getDeviceCapability('gif'))
				$MobileJoomla_Device['imageformats'][] = 'gif';
			if($wurflObj->getDeviceCapability('jpg'))
				$MobileJoomla_Device['imageformats'][] = 'jpg';
			if($wurflObj->getDeviceCapability('wbmp'))
				$MobileJoomla_Device['imageformats'][] = 'wbmp';
		}
		else
			$MobileJoomla_Device['markup'] = '';
	}

	function onGetDatabaseSize()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$db->setQuery('SHOW TABLE STATUS FROM `'.$app->getCfg('db').'` LIKE '.$db->Quote($app->getCfg('dbprefix').'TeraWurfl%'));
		$result = $db->loadObjectList();

		$size = 0;
		foreach($result as $row)
			$size += $row->Data_length;

		$db->setQuery('SELECT value FROM #__TeraWurflSettings WHERE id='.$db->Quote('wurfl_version'));
		$date = $db->loadResult();
		if(preg_match('#\d\d\d\d-\d\d-\d\d#', $date, $match))
			$date = $match[0];
		else
			$date = '';

		return $size ? array('ScientiaMobile DB-API (AGPL)', $size, $date) : null;
	}
}
