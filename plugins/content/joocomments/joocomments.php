<?php
/*
 * $Id$
 *	v1.0.0
 * Friday Sep-02-2011
 * @component JooComments
 * @copyright Copyright (C) Abhiram Mishra www.bullraider.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport ( 'joomla.application.component.model' );

class plgContentJooComments extends JPlugin
{
	function plgContentJooComments( &$subject, $config )
	{
		parent::__construct( $subject, $config );
		$this->loadLanguage('com_joocomments',JPATH_SITE);
	}

	//display top of content
	function onContentAfterDisplay($context, &$row, &$params, $page=0){
		$app = JFactory::getApplication();
		if ( $app->isAdmin() ) { return; }
		if(!file_exists(JPATH_SITE.DS.'components'.DS.'com_joocomments'.DS.'joocomments.php')){
			return '';
		}
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd("view");
		if($option != "com_content" ){
			return '';
		}
		$var=$row->params->toArray();
		$isCommentsEnabled=array_key_exists('comments_enabled', $var);
		$isCommentsEnabled=$isCommentsEnabled ?($var['comments_enabled']=='0'?false:true): !$isCommentsEnabled;

		if($isCommentsEnabled==false){
			return '';
		}
		if($view == "article"){
			$isCommentsAllowed=array_key_exists('comments_allowed', $var);
			$isCommentsAllowed=$isCommentsAllowed ? ($var['comments_allowed']=='0'?false:true):$isCommentsAllowed;
			$this->commentDisplay($row,$isCommentsAllowed);
		}
	}
	function commentDisplay(&$article,$isCommentsAllowed)
	{
		$view = JRequest::getVar('view');
		$app = JFactory::getApplication();
		//make sure the mootools-more.js is loaded
		JHtml::_('behavior.framework',true);
		$doc = JFactory::getDocument();

		require_once dirname(__FILE__).DS.'helpers'.DS.'Helper.php';
		if(file_exists(JPATH_SITE.DS.'components'.DS.'com_joocomments'.DS.'joocomments.php')){
			if ( $app->isAdmin() ) { return; }
			ob_start();
			?>

<div id="comment-wrapper"></div>
<?php if($isCommentsAllowed==true) echo JText::sprintf('COM_JOOCOOMENTS_NEW_COMMENTS_DISABLED').'<div id="comments"></div>';?>

			<?php
			//need to add these stylesheet in the header,Which JHTML:stylesheet() will do,
			JHTML::stylesheet(JURI::root().'components/com_joocomments/assets/css/main.css');
			JHTML::stylesheet(JURI::root().'components/com_joocomments/assets/css/wmd.css');
			?>
<script
	src='<?php echo JURI::root().'components/com_joocomments/assets/js/showdown.js';?>'
	type='text/javascript'></script>
<script language="javascript" type="text/javascript"><!--
            //<![CDATA[
            var currentLanguage='<?php echo $doc->language;?>';
            var arr=Locale.list();
           // check if current language is already available in Locale
           if(!currentLanguage.substring(arr)){
               <?php 
               		echo 'Locale.define(\''.Helper::languageString($doc->language).'\', \'FormValidator\', {';
               		echo 'required: \''.JText::sprintf('COM_JOOCOOMENTS_COMMENTS_FORM_FIELD_REQUIRED_ERROR').'\',';
               		echo 'email: \''.JText::sprintf('COM_JOOCOOMENTS_COMMENTS_FORM_FIELD_EMAIL_WRONG_ERROR').'\',';
               		echo 'errorPrefix: \''.JText::sprintf('COM_JOOCOOMENTS_COMMENTS_FORM_FIELD_ERROR_PREFIX').'\',';
               		echo '});'
               		?>
           }           
	   		Locale.use('<?php echo Helper::languageString($doc->language);?>');
            var article_id="<?php echo $article->id; ?>";
            window.addEvent("load", function() {
                //comments are not closed ie 0 then only load.
            	<?php  if($isCommentsAllowed==false){ ?>
            	var url = window.location.hash;
               	url = "<?php echo JURI::base(); ?>index.php?option=com_joocomments&random=" + Math.random();           	            	        
				var req = new Request({
					 method: 'get',
                         url: url,                         
                         onComplete: function(text) { 
						document.id('comment-wrapper').innerHTML = document.id('comment-wrapper').innerHTML + text;	
						loadTextEditor();																	 						
                        }						
					}).send();		
				<?php }else{?>
				showComments();
				<?php }?>			 							
            	});	
            <?php  if($isCommentsAllowed==false){ ?>
            function loadTextEditor(){
                wmd_options.ajaxForm=true;   
            	Attacklab.wmd();
            	Attacklab.wmdBase();
            	Attacklab.Util.startEditor();
            	initialize("<?php echo JURI::base();?>index.php?option=com_joocomments");
                var myForm = document.id('myForm'),
                myResult = document.id('myResult');

            // Labels over the inputs.
            myForm.getElements('[type=text], textarea').each(function(el){
                new OverText(el);
            });

            // Validation.
            var formValidator=new Form.Validator.Inline(myForm,{evaluateFieldsOnBlur:false,
                												evaluateFieldsOnChange:false,
                												onFormValidate:function(s,ele,on){
																if(!s){
																refreshCaptcha();
																document.id('captchaText').value="";
																}
            	
																}
																}
														);
            formValidator.add('captchaValidator',{errorMsg:'<?php echo JText::sprintf('COM_JOOCOOMENTS_COMMENTS_FORM_CAPTCHA_ERROR');?>',
                									  test:function(field){
				  									return validateCaptcha(field);
				  									  }
			  									  }
			  				);

            // Ajax (integrates with the validator).
            new Form.Request(myForm, myResult, {onComplete:function(){refreshCaptcha();}
                ,async:false,
                requestOptions: {
                    'spinnerTarget': myForm
                },
                extraData: { // This is just to make this example work.
                    'article_id': article_id
             			  									  }
             			  								  });
            	showComments();
            }<?php }?>
            function showComments(){
            	var commentblock = document.id('comments');
            	var parameter="&article_id="+article_id;
            	var htmlRequest = new Request.HTML({url: '<?php echo JURI::base();?>index.php?option=com_joocomments&task=showComments&view=commentpage'+parameter,
            		onRequest: function(){
            		document.id('comments').set('text', '<?php  echo JText::sprintf('COM_JOOCOOMENTS_COMMENTS_LIST_LOADING');?>');
                },
                onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript){
                	document.id('comments').empty();
                    document.id('comments').innerHTML=responseHTML;
                      refreshCaptcha();
            	}}).send();
            }
				//]]>						
				--></script>
<script
	src='<?php echo JURI::root().'components/com_joocomments/assets/js/wmd.js';?>'
	type='text/javascript'></script>
			<?php
			$output = ob_get_contents();
			ob_end_clean();
//                        $article->text=$article->text.$output.'<center>Powered by <a href="http://www.bullraider.com" title="bullraider">Bullraider.com</a></center>';
			$article->text=$article->text.$output.'<center></center>';
			JHTML::script(JURI::root().'components/com_joocomments/assets/js/main.js');
			return true;
		}else{
			return false;
		}
	}
}