<?php
/*
 * v1.0.0
 * Friday Sep-02-2011
 * @component JooComments
 * @copyright Copyright (C) Abhiram Mishra www.bullraider.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport ( 'joomla.application.component.model' );

class JooCommentsModelComments extends JModel {

	var $_table=null;

	function __construct() {
		parent::__construct ();
	
	}
public function getTable($type = 'Comments', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	function store($post=0, $insert=0) {
		JModel::addTablePath(JPATH_SITE.DS.'components'.DS.'com_joocomments' . DS . 'tables');
		$row = $this->getTable( 'Comments', 'Table' );
		if(!$post){
			$post = $this->getState ( 'request' );
		}
		if (! $row->bind ( $post )) {
			JError::raiseWarning ( 1, $row->getError ( true ) );
			return false;
		}
		$row->save($post);
	}
	function retriveComments($article_id){
		// I should use query construction object instead of the way below
		$query='SELECT  c.name , c.comment FROM #__joocomments as c where published="1" and '.'article_id='.(int) $article_id;
		$db =& JFactory::getDBO();
		$db->setQuery($query);

		return $db->loadAssocList();
	}
}