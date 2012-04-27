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

class JooCommentsModelCommentpage extends JModel {
function retriveComments($article_id){
		// I should use query construction object instead of the way below
		$query='SELECT  c.name , c.comment,c.publish_date FROM #__joocomments as c where published="1" and '.'article_id='.(int) $article_id.' order by publish_date desc,id desc';
		$db =& JFactory::getDBO();
		$db->setQuery($query);

		return $db->loadAssocList();
	}
}