<?php
/*
 * v1.0.0
 * Friday Sep-02-2011
 * @component JooComments
 * @copyright Copyright (C) Abhiram Mishra www.bullraider.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.modeladmin');
class JooCommentsModelComment extends JModelAdmin
{
	protected	$option 		= 'com_joocomments';
	protected 	$text_prefix	= 'com_joocomments';
	
	public function getForm($data = array(), $loadData = true) {
		
		$app	= JFactory::getApplication();
		$form 	= $this->loadForm('com_joocomments.comment', 'comment', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	public function &getTable($name='commens',$prefix='JooCommentsTable',$options = array()){
		$table=parent::getTable('comments','JooCommentsTable',$options);
		return $table;
	}
	
}
	