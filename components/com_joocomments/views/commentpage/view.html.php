<?php
/*
 * v1.0.0
 * Friday Sep-02-2011
 * @component JooComments
 * @copyright Copyright (C) Abhiram Mishra www.bullraider.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.view' );
class JooCommentsViewCommentpage extends JView {
	/**
	 * Display the view
	 */
	function display($tmpl = null) {
	$model =  $this->getModel ();
	$article_id=JRequest::getInt('article_id',null,'get');
	$this->comments=$model->retriveComments($article_id);
	include_once(dirname(__FILE__).DS.'..'.DS.'..'.DS.'helpers'.DS.'markdown.php');
	parent::display($tmpl);
} } ?>
