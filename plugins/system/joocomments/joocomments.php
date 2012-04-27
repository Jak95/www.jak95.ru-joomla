<?php
/*
 * v1.0.0
 * Friday Sep-02-2011
 * @component JooComments
 * @copyright Copyright (C) Abhiram Mishra www.bullraider.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgSystemJooComments extends JPlugin{

	function __construct(&$subject, $config) {
		parent::__construct ( $subject, $config );
		$this->loadLanguage();
	}

	function onContentPrepareForm($form, $data) {
		jimport('joomla.form.form');
		if ($form->getName()=='com_content.article')
		{
			JForm::addFormPath(dirname(__FILE__).'/article');
			$form->loadFile('attribs', false);
			
			if(!empty($data))
			if($data->id==0){
				//disable the comments_allowed if the article is a new article
				$form->removeField('comments_allowed', 'attribs');
			}else{//see if there is no comments already posted
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);

				$query->select('com.id');
				// From the hello table
				$query->from('#__joocomments as com');
				$query->where('com.article_id='.$data->id);
				$db->setQuery($query);
				$numOfArticles=count($db->loadAssocList());
				if($numOfArticles==0){
					$form->removeField('comments_allowed', 'attribs');
				}
			}
		}
	}
}
