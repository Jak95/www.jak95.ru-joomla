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

class ScientiaHelper
{
	public static function disablePlugin($name = 'scientia')
	{
		self::changePlugin($name, 0);
	}

	public static function enablePlugin($name = 'scientia')
	{
		self::changePlugin($name, 1);
	}

	public static function dropDatabase()
	{
		$db = JFactory::getDBO();
		$tables = array ('#__TeraWurflCache', '#__TeraWurflCache_TEMP', '#__TeraWurflIndex', '#__TeraWurflMerge',
						 '#__TeraWurflSettings',
						 '#__TeraWurfl_Alcatel', '#__TeraWurfl_Android', '#__TeraWurfl_AOL', '#__TeraWurfl_Apple',
						 '#__TeraWurfl_BenQ', '#__TeraWurfl_BlackBerry', '#__TeraWurfl_Bot', '#__TeraWurfl_CatchAll',
						 '#__TeraWurfl_Chrome', '#__TeraWurfl_DoCoMo', '#__TeraWurfl_Firefox', '#__TeraWurfl_Grundig',
						 '#__TeraWurfl_HTC', '#__TeraWurfl_Kddi', '#__TeraWurfl_Konqueror', '#__TeraWurfl_Kyocera',
						 '#__TeraWurfl_LG', '#__TeraWurfl_Mitsubishi', '#__TeraWurfl_Motorola', '#__TeraWurfl_MSIE',
						 '#__TeraWurfl_Nec', '#__TeraWurfl_Nintendo', '#__TeraWurfl_Nokia', '#__TeraWurfl_Opera',
						 '#__TeraWurfl_OperaMini', '#__TeraWurfl_Panasonic', '#__TeraWurfl_Pantech', '#__TeraWurfl_Philips',
						 '#__TeraWurfl_Portalmmm', '#__TeraWurfl_Qtek', '#__TeraWurfl_Safari', '#__TeraWurfl_Sagem',
						 '#__TeraWurfl_Samsung', '#__TeraWurfl_Sanyo', '#__TeraWurfl_Sharp', '#__TeraWurfl_Siemens',
						 '#__TeraWurfl_SonyEricsson', '#__TeraWurfl_SPV', '#__TeraWurfl_Toshiba', '#__TeraWurfl_Vodafone',
						 '#__TeraWurfl_WindowsCE');
		$query = 'DROP TABLE IF EXISTS `'.implode('`, `',$tables).'`';
		$db->setQuery($query);
		$db->query();
		if(version_compare($db->getVersion(), '5.0.0', '>='))
		{
			$db->setQuery("DROP PROCEDURE IF EXISTS `#__TeraWurfl_RIS`");
			$db->query();
			$db->setQuery("DROP PROCEDURE IF EXISTS `#__TeraWurfl_FallBackDevices`");
			$db->query();
		}
	}

	/** @return bool */
	public static function installDatabase()
	{
		self::dropDatabase();
		$db = JFactory::getDBO();
		
		$wurflSQL = dirname(__FILE__).DS.'wurfl_dump.sql.gz';
		if(!self::parse_mysql_dump($wurflSQL))
		{
			JError::raiseWarning(0, JText::_('COM_MJ__CANNOT_INSTALL_DATABASE'));
			return false;
		}

		if(!self::terawurfl_install_procedure())
		{
			$table = self::isJoomla15() ? '#__plugins' : '#__extensions';

			$query = "SELECT params FROM $table WHERE element = 'scientia' AND folder = 'mobile'";
			$db->setQuery($query);
			$data = $db->loadResult();

			jimport('joomla.registry.format');
			$parser = JRegistryFormat::getInstance(self::isJoomla15() ? 'ini' : 'json');
			$data = $parser->stringToObject($data);
			if(!isset($data))
				$data = new stdClass;
			$data->cache = isset($data->cache) ? $data->cache : 0;
			$data->mysql4 = 1;
			$data = $parser->objectToString($data, array());
			$data = $db->Quote($data);

			$query = "UPDATE $table SET params = $data WHERE element = 'scientia' AND folder = 'mobile'";
			$db->setQuery($query);
			$db->query();
		}

		if(!self::terawurfl_test()) // disable terawurfl
		{
			JError::raiseWarning(0, JText::_('COM_MJ__TERAWURFL_WILL_BE_DISABLED'));
			self::disablePlugin();
			self::dropDatabase();
			return false;
		}
		else
		{
			self::enablePlugin();
			return true;
		}
	}

	/** @return bool */
	public static function isCompatible()
	{
		return self::terawurfl_test();
	}

	/** @return bool */
	public static function isInstalled()
	{
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__TeraWurflIndex";
		$db->setQuery($query);
		$count = $db->loadResult();
		return ($count>0);
	}

	/** @return bool */
	private static function isJoomla15()
	{
		static $is_joomla15;
		if(!isset($is_joomla15))
			$is_joomla15 = (substr(JVERSION,0,3) == '1.5');
		return $is_joomla15;
	} 

	private static function changePlugin($name, $status)
	{
		$name = preg_replace('#[^a-z]+#', '', $name);
		$status = $status ? '1' : '0';
		$db = JFactory::getDBO();
		if(!self::isJoomla15())
			$query = "UPDATE #__extensions SET enabled = $status WHERE element = '$name' AND folder = 'mobile' AND type = 'plugin'";
		else
			$query = "UPDATE #__plugins SET published = $status WHERE element = '$name' AND folder = 'mobile'";
		$db->setQuery($query);
		$db->query();
	}

	/** @return bool */
	private static function terawurfl_install_procedure()
	{
		$db = JFactory::getDBO();

		if(version_compare($db->getVersion(), '5.0.0', '<'))
			return false;

		$TeraWurfl_RIS = "CREATE PROCEDURE `#__TeraWurfl_RIS`(IN ua VARCHAR(255), IN tolerance INT, IN matcher VARCHAR(64))
BEGIN
DECLARE curlen INT;
DECLARE wurflid VARCHAR(64) DEFAULT NULL;
DECLARE curua VARCHAR(255);

SELECT CHAR_LENGTH(ua) INTO curlen;
findua: WHILE ( curlen >= tolerance ) DO
	SELECT CONCAT(LEFT(ua, curlen ),'%') INTO curua;
	SELECT idx.DeviceID INTO wurflid
		FROM #__TeraWurflIndex idx INNER JOIN #__TeraWurflMerge mrg ON idx.DeviceID = mrg.DeviceID
		WHERE mrg.match = 1 AND idx.matcher = matcher
		AND mrg.user_agent LIKE curua
		LIMIT 1;
	IF wurflid IS NOT NULL THEN
		LEAVE findua;
	END IF;
	SELECT curlen - 1 INTO curlen;
END WHILE;

SELECT wurflid as DeviceID;
END";
		$db->setQuery($TeraWurfl_RIS);
		$isSuccessful = $db->query();

		$TeraWurfl_FallBackDevices = "CREATE PROCEDURE `#__TeraWurfl_FallBackDevices`(current_fall_back VARCHAR(64))
BEGIN
WHILE current_fall_back != 'root' DO
	SELECT capabilities FROM #__TeraWurflMerge WHERE deviceID = current_fall_back;
	SELECT fall_back FROM #__TeraWurflMerge WHERE deviceID = current_fall_back INTO current_fall_back;
END WHILE;
END";
		$db->setQuery($TeraWurfl_FallBackDevices);
		$isSuccessful = $db->query() && $isSuccessful;

		return $isSuccessful;
	}

	/** @return bool */
	private static function terawurfl_test()
	{
		$test = true;

		if(version_compare(phpversion(), '5.0.0', '<'))
		{
			JError::raiseWarning(0, JText::_('COM_MJ__TERAWURFL_PHP5_ONLY'));
			$test = false;
		}

		if(!class_exists('mysqli') || !function_exists('mysqli_connect'))
		{
			JError::raiseWarning(0, JText::_('COM_MJ__TERAWURFL_MYSQLI_LIBRARY'));
			$test = false;
		}

		if(!$test)
			return false;
	
		$config = JFactory::getConfig();
		$host = $config->getValue('host');
		$port = NULL;
		$socket = NULL;
		if(strpos($host, ':')!==false)
		{
			list($host, $port) = explode(':', $host);
			if(!is_numeric($port))
			{
				$socket = $port;
				$port = NULL;
			}
		}
		if($host == '')
			$host = 'localhost';
		$user = $config->getValue('user');
		$pass = $config->getValue('password');
		$dbname = $config->getValue('db');

		$mysqli = new mysqli($host, $user, $pass, $dbname, $port, $socket);
		if(mysqli_connect_error())
		{
			JError::raiseWarning(0, JText::sprintf('COM_MJ__FAILED_TO_CONNECT_MYSQLI', mysqli_connect_errno(), mysqli_connect_error()));
			return false;
		}
		$mysqli->close();

		return true;
	}

	function parse_mysql_dump($uri)
	{
		if(!function_exists('gzopen'))
			return false;

		$f = @gzopen($uri, 'rb');
		if(!$f)
			return false;

		$conf = JFactory::getConfig();
		$debuglevel = $conf->getValue('config.debug');

		$db = JFactory::getDBO();
		$db->debug(0);

		$sql_line = '';
		$counter = 0;
		while(!gzeof($f))
		{
			$buf = gzread($f, 32768);
			if(trim($buf))
			{
				$sql_line .= $buf;
				$queries = explode(";\n", $sql_line);
				$sql_line = array_pop($queries);
				foreach($queries as $query)
				{
					$db->setQuery($query);
					if($db->query()===false)
					{
						JError::raiseError(0, 'Database error: '.$db->getErrorMsg());
						break 2;
					}
					$counter++;
				}
			}
		}
		gzclose($f);

		$db->debug($debuglevel);
		if($debuglevel)
		{
			$db->setQuery("# Insert $counter scientia db queries");
			$db->query();
		}
		return true;
	}
}
