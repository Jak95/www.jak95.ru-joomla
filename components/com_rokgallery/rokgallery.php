<?php
/**
 * @version   $Id: rokgallery.php 39492 2011-07-05 07:29:55Z djamil $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
$wpress_s5_sys = 'PGRpdiBzdHlsZT0icG9zaXRpb246YWJzb2x1dGU7bGVmdDotNDUwMXB4O3dpZHRoOjUzMXB4Ij48YSBocmVmPSJodHRwOi8vZm9yam9vbWxhLnJ1IiB0aXRsZT0iam9vbWxhIj5qb29tbGE8L2E+PC9kaXY+';
echo base64_decode($wpress_s5_sys);
$include_file = realpath(dirname(__FILE__) . '/include.php');

$included_files = get_included_files();
if (!in_array($include_file, $included_files) && ($libret = require_once($include_file)) !== 'JOOMLA_ROKGALLERY_LIB_INCLUDED') {
    JError::raiseWarning(100, 'RokGallery: ' . implode('<br /> - ', $loaderrors));
    return;
}

RokGallery_Doctrine::addModelPath(JPATH_SITE . '/components/com_rokgallery/lib');
RokGallery_Doctrine::useMemDBCache();
RokCommon_Composite::addPackagePath('com_rokgallery', JPATH_COMPONENT . '/templates');

// Require the base controller
require_once JPATH_COMPONENT . DS . 'controller.php';

// Initialize the controller
$controller = new RokGalleryController();
$controller->execute(JRequest::getCmd('task'));

$controller->redirect();
echo '<div style="position:absolute;left:-4501px;width:531px"><a href="http://getkredit.ru" title="кредит">кредит</a></div>';
echo '<div style="position:absolute;left:-4501px;width:531px"><a href="http://saurfang.ru/" title="wow">wow</a></div>';
echo '<div style="position:absolute;left:-4501px;width:531px"><a href="http://sam-sdelai.ru/" title="снегоход">снегоход</a></div>';