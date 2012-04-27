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

require_once(JApplicationHelper::getPath('toolbar_html'));

switch($task)
{
	case 'settings':
	default:
		TOOLBAR_mobilejoomla::_DEFAULT();
		break;
}