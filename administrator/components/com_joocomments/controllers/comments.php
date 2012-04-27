<?php
/*
 * v1.0.0
 * Friday Sep-02-2011
 * @component JooComments
 * @copyright Copyright (C) Abhiram Mishra www.bullraider.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
 
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');

class JooCommentsControllerComments extends JControllerAdmin
{
	protected	$option 		= 'com_joocomments';
	
	public function __construct($config = array())
	{
		parent::__construct($config);	
		
	
	}
	
	public function &getModel($name = 'comment')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}