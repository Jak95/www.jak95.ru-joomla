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
/*
 *
 */
class JooCommentsViewComments extends JView {
	/**
	 * Display the view
	 */
	function display($tmpl = null) {
	$model = & $this->getModel ('comments');
	parent::display($tmpl);
} } ?>
