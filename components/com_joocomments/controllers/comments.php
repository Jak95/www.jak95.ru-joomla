<?php
/*
 * v1.0.0
 * Friday Sep-02-2011
 * @component JooComments
 * @copyright Copyright (C) Abhiram Mishra www.bullraider.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class JooCommentsControllerComments extends JooCommentsController {

	function __construct($default = array()) {
		parent::__construct ( $default );
		require_once JPATH_SITE.DS.'components'.DS.'com_joocomments'.DS.'captcha'.DS.'JooCaptcha.php';
		$this->registerTask('postComment','postComment');
		//$this->registerTask('checkCaptcha', 'invalid');
	}

	function showcaptcha(){
		$captcha = new JooCaptcha();
		$captcha->CaptchaSecurityImages();
		die();
	}
	function checkCaptcha(){
		$captcha = new JooCaptcha();	
		$code=htmlentities(JRequest::getString('userCaptcha'));
		$text=$captcha->check($code);
		$document =& JFactory::getDocument();
		echo $text;
		$captcha->destroy();
		die();
	}
	function showComments(){
	//$model = & $this->getModel ('comments');
	//$article_id=JRequest::getInt('article_id',null,'post');
	//$comments=$model->retriveComments($article_id);
	//$view=$this->getView();
	//echo $view;
	parent::display();
	/*
		// Get the document object.
		$document =& JFactory::getDocument();
		// Set the MIME type for JSON output.
		$document->setMimeEncoding( 'application/json' );
		// Change the suggested filename.
		JResponse::setHeader( 'Content-Disposition', 'attachment; filename="'.$this->getName().'.json"' );
		// Output the JSON data.
		$commentsList['comments']=$comments;
		echo json_encode( $commentsList );*/
		die();

	}
	function postComment(){
		JRequest::checkToken() or die(JText::sprintf('COM_JOOCOOMENTS_COMMENTS_FORM_EXPIRED_TIME'));
		$document =& JFactory::getDocument();
		$post['name']=htmlentities(JRequest::getString( 'userName',null,'post',JREQUEST_ALLOWHTML), ENT_QUOTES, "UTF-8");
		$post['email']=htmlentities(JRequest::getString('userEmail',null,'post',JREQUEST_ALLOWHTML), ENT_QUOTES, "UTF-8");
		$post['website']=htmlentities(JRequest::getString('userWebsite',null,'post',JREQUEST_ALLOWHTML), ENT_QUOTES, "UTF-8");
		$post['article_id']=htmlentities(JRequest::getInt('article_id',null,'post'), ENT_QUOTES, "UTF-8");
		// removed unnecessary field$post['article_title']=htmlentities(JRequest::getString('article_title',null,'post',JREQUEST_ALLOWHTML),ENT_QUOTES, "UTF-8");
		$post['comment']=htmlentities(JRequest::getString('userComment',null,'post',JREQUEST_ALLOWHTML),ENT_NOQUOTES, "UTF-8");
		$post['publish_date']= date("Y-m-d H:i:s");
		$post['published']='0';
		$model = & $this->getModel ('comments');
		$model->store($post);
		echo JText::sprintf('COM_JOOCOOMENTS_COMMENTS_FORM_SUCCESSFUL_SUBMIT');
		die();
	}
}
?>
