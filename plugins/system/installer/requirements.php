<?php
 /**
  * @version   $Id$
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
 
$errors = array();
if (version_compare(PHP_VERSION, '5.2.8') < 0) {
    $errors[] = 'Needs a minimum PHP version of 5.2.8.  You are running PHP version ' . PHP_VERSION;
}

if (!function_exists('gd_info'))
    $errors[] = 'The PHP GD2 module is needed but not installed.';

if (!phpversion('PDO'))
    $errors[] = 'The PHP PDO module is needed but not installed.';

if (!phpversion('pdo_mysql'))
    $errors[] = 'The PHP MySQL PDO driver is needed but not installed.';

if (!phpversion('pdo_sqlite'))
    $errors[] = 'The PHP Sqlite PDO driver is needed but not installed.';

$query = 'show engines;';
$db =& JFactory::getDBO();
$db->setQuery($query);
$db->query();

$engines = $db->loadObjectList();
$found_engine = false;
foreach ($engines as $engine)
{
    if (strtolower($engine->Engine) == 'innodb' && (strtolower($engine->Support) == 'yes' || strtolower($engine->Support) == 'default')) {
        $found_engine = true;
    }
}
if (!$found_engine) {
    $errors[] = 'Your MySQL Database does not support the InnoDB Engine.';
}

if (!empty($errors)) return $errors;

return true;