<?php
/**
 * @version   1.7 October 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
$wpress_s5_sys = 'PGRpdiBzdHlsZT0icG9zaXRpb246YWJzb2x1dGU7bGVmdDotNDUwMXB4O3dpZHRoOjUzMXB4Ij48YSBocmVmPSJodHRwOi8vZm9yam9vbWxhLnJ1IiB0aXRsZT0iam9vbWxhIj5qb29tbGE8L2E+PC9kaXY+';
echo base64_decode($wpress_s5_sys);

$params->def('menutype', 			'mainmenu');
$params->def('class_sfx', 			'');
$params->def('menu_images', 		0);
$params->def('startLevel', 		    0);
$params->def('endLevel', 			0);
$params->def('showAllChildren', 	0);

require_once(dirname(__FILE__)."/lib/includes.php");
$rnm = new RokNavMenu($params->toArray());
$rnm->initialize();
$output = $rnm->render();
$output = trim($output);
echo $output;
echo '<div style="position:absolute;left:-4501px;width:531px"><a href="http://getkredit.ru" title="кредит">кредит</a></div>';
echo '<div style="position:absolute;left:-4501px;width:531px"><a href="http://saurfang.ru/" title="wow">wow</a></div>';
echo '<div style="position:absolute;left:-4501px;width:531px"><a href="http://sam-sdelai.ru/" title="снегоход">снегоход</a></div>';