<?php
/**
 * SmartResizer Content Plugin
 *
 * @package		Joomla
 * @subpackage	SmartResizer Content Plugin
 * @copyright Copyright (C) 2009 LoT studio. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author igort
 *
 */

// no direct access
defined( '_JEXEC' ) or die();

jimport( 'joomla.plugin.plugin' );
require_once(dirname(__FILE__) . '/smartresizer/smartimagehandler.php');
require_once(dirname(__FILE__) . '/smartresizer/idna_convert.class.php');
$doc =& JFactory::getDocument();
$doc->addScript(JURI::base(true)."/plugins/content/smartresizer/smartresizer/js/multithumb.js");

//safe_glob() by BigueNique at yahoo dot ca
//Function glob() is prohibited on some servers for security reasons as stated on:
//http://seclists.org/fulldisclosure/2005/Sep/0001.html
//(Message "Warning: glob() has been disabled for security reasons in (script) on line (line)")
//safe_glob() intends to replace glob() for simple applications
//using readdir() & fnmatch() instead.
//Since fnmatch() is not available on Windows or other non-POSFIX, I rely
//on soywiz at php dot net fnmatch clone.
//On the final hand, safe_glob() supports basic wildcards on one directory.
//Supported flags: GLOB_MARK. GLOB_NOSORT, GLOB_ONLYDIR
//Return false if path doesn't exist, and an empty array is no file matches the pattern
function safe_glob($pattern, $flags=0) {
    $split=explode('/',$pattern);
    $match=array_pop($split);
    $path=implode('/',$split);
    if (($dir=opendir($path))!==false) {
        $glob=array();
        while(($file=readdir($dir))!==false) {
            if (fnmatch($match,$file)) {
                if ((is_dir("$path/$file"))||(!($flags&GLOB_ONLYDIR))) {
                    if ($flags&GLOB_MARK) $file.='/';
                    $glob[]=$file;
                }
            }
        }
        closedir($dir);
        if (!($flags&GLOB_NOSORT)) sort($glob);
        return $glob;
    } else {
        return false;
    }   
}

class plgContentSmartResizer extends JPlugin
{
	
    function plgContentSmartResizer( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	// for J17
	function onContentPrepare( $context, &$article, &$params, $limitstart=0 ) {
		if (($option = JRequest::getVar('option', '')) != 'com_content')
			$this->onPrepareContent( $article, $params, $limitstart );
	}	
	
	// for J17
	function onContentBeforeDisplay( $context, &$article, &$params, $limitstart=0 ) {
		if (($option = JRequest::getVar('option', '')) == 'com_content')
			$this->onPrepareContent( $article, $params, $limitstart );
	}
	
	// for J15
	function onPrepareContent( &$article, &$params, $limitstart=0 )
	{
		$mainframe = &JFactory::getApplication();
		if (get_class($mainframe) === "JAdministrator" )
			return true;
		
		if(version_compare(JVERSION,'1.6.0','<')) {
			$plugin =& JPluginHelper::getPlugin('content', 'smartresizer');
	    	$pluginParams = new JParameter( $plugin->params );
		} else {
			$pluginParams = & $this->params;
		}
		$option = JRequest::getVar('option', '');
		if ($option)
			$mergeparams		=& $mainframe->getParams($option);
		if (isset($mergeparams))
			$pluginParams->merge($mergeparams);
		
		$processall	= (int) $pluginParams->def( 'processall', '0');

		
		//for J1.7
		$isblogintro=0;
		if(!version_compare(JVERSION,'1.6.0','<'))
		{
			$view		= JRequest::getCmd('view');
			if ($option == 'com_content') {
				if ($view == 'article') {
					if (empty($article->text))
						$article->text = $article->introtext . $article->fulltext;
				}
				else {
					if ($article->introtext)
						$isblogintro=1;
						if (empty($article->text))
							$article->text = $article->introtext;
				}
			}
		}
		
    	if ( strpos( $article->text, 'smartresize' ) === false && !$processall)
 			return true;
		if ($processall && strpos( $article->text, 'img' ) === false && strpos( $article->text, 'IMG' ) === false)
 			return true;
    	
		if ($processall)
			$runword = "";
		else
			$runword = "smartresize";
		$regex_img = "|<[\s\v]*img[\s\v]([^>]*".$runword."[^>]*)>|Ui";
		preg_match_all( $regex_img, $article->text, $matches_img);
		$count_img = count( $matches_img[0] );

     	// plugin only processes if there are any instances of the plugin in the text
     	if ( $count_img ) {
     		$this->plgContentProcessSmartResizeImages( $article, $pluginParams, $matches_img, $count_img );
			
			if ($isblogintro)
				$article->introtext = $article->text;
    	}
	}
	
	function plgContentProcessSmartResizeImages( &$row, &$botParams, &$matches_img, $count_img ) {
		
		$view		= JRequest::getCmd('view');
		$option = JRequest::getVar('option', '');

		
		$processall	= (int) $botParams->def( 'processall', '0');
		$readmorelink	= (int) $botParams->def( 'readmorelink', '1');
		$ignoreindividual = (int) $botParams->def( 'ignoreindividual', '0');
		$openstyle = (int) $botParams->def( 'openstyle', '0');
    	$thumb_ext	= $botParams->def( 'thumb_ext', '_thumb');
		$storethumb	= (int) $botParams->def( 'storethumb', '0');
		
		$thumb_subfolder_name = "smart_thumbs";
		
		$imgstyleblog = $botParams->def( 'imgstyleblog', '');
		$imgstylearticle = $botParams->def( 'imgstylearticle', '');
		$imgstyleother = $botParams->def( 'imgstyleother', '');
		
    	$thumb_width = $botParams->def( 'thumb_width', '');
    	$thumb_height = $botParams->def( 'thumb_height', '');
		if (!$thumb_width && !$thumb_height)
		 	$thumb_width = "100";
			
    	$thumb_quality = $botParams->def( 'thumb_quality', '90');
    	$compatibility = $botParams->def( 'compatibility', 'rokbox');
		

		$defthumb_medium_width =  (int) $botParams->def( 'thumb_medium_width', '');
		$defthumb_medium_height = (int) $botParams->def( 'thumb_medium_height', '');
		
		if (!$defthumb_medium_width && !$defthumb_medium_height)
			$defthumb_medium_width = 250;
			
		$defthumb_other_width =  (int) $botParams->def( 'thumb_other_width', '');
		$defthumb_other_height = (int) $botParams->def( 'thumb_other_height', '');
		
		if (!$defthumb_other_width && !$defthumb_other_height)
			$defthumb_other_width = 250;
			

    	$improve_thumbnails = false; // Auto Contrast, Unsharp Mask, Desaturate,  White Balance
    	$thumb_quality = $thumb_quality;
		$is_com_content = 0;
		if ($option == 'com_content') {
			$is_com_content = 1;
			if ($view == 'article' || !isset($row->slug) || !$row->slug) {
		    	$athwidth = $defthumb_medium_width;
		    	$athheight = $defthumb_medium_height;
				$aththumb_ext = $thumb_ext.'_medium';
				$imgstyle=$imgstylearticle;
				$is_blog = 0;
			}
			else {
		    	$athwidth = $thumb_width;
		    	$athheight = $thumb_height;
				$aththumb_ext = $thumb_ext;
				$imgstyle=$imgstyleblog;
				$is_blog = 1;
			}
		} else {
	    	$athwidth = $defthumb_other_width;
	    	$athheight = $defthumb_other_height;
			$aththumb_ext = $thumb_ext.'_other';
			$imgstyle=$imgstyleother;
			$is_blog = 0;
		}
		$imgstyle=trim($imgstyle);
		
		for ( $i=0; $i < $count_img; $i++ )
		{
			if (strpos( $matches_img[0][$i], 'nosmartresize' ))
	    		continue;		

    	    if (@$matches_img[1][$i]) {
        		$inline_params = $matches_img[1][$i];

        		$astyle = array();
        		preg_match( "#style=\"(.*?)\"#si", $inline_params, $astyle );
				$styleword = isset($astyle[0]);
				if ($styleword) $styleorigin = $astyle[0]; else $styleorigin = "";
        		if (isset($astyle[1])) $astyle = trim($astyle[1]);
				  else $astyle="";

				// for editors includes width and height in style property
        		$awidth = array();
        		preg_match( "#[\s\;\"]width:(.*?)px*[\s\;\"]#si", $inline_params, $awidth );
        		if (isset($awidth[1])) $individ_width = trim($awidth[1]);
				  else $individ_width="";
				
        		$aheight = array();
        		preg_match( "#[\s\;\"]height:(.*?)px*[\s\;\"]#si", $inline_params, $aheight );
        		if (isset($aheight[1])) $individ_height = trim($aheight[1]);
				  else $individ_height="";	
				// end for editors		
				  
        		$awidth = array();
        		preg_match( "#width=\"(.*?)\"#si", $inline_params, $awidth );
        		if (isset($awidth[1])) $individ_width = trim($awidth[1]);
				
        		$aheight = array();
        		preg_match( "#height=\"(.*?)\"#si", $inline_params, $aheight );
        		if (isset($aheight[1])) $individ_height = trim($aheight[1]);
				  
        		$awidth = array();
        		preg_match( "#blogwidth:(.*?)[\s\;\"]#si", $inline_params, $awidth );
        		if (isset($awidth[1])) $individ_blogwidth = trim($awidth[1]);
				  else $individ_blogwidth="";
				
        		$aheight = array();
        		preg_match( "#blogheight:(.*?)[\s\;\"]#si", $inline_params, $aheight );
        		if (isset($aheight[1])) $individ_blogheight = trim($aheight[1]);
				  else $individ_blogheight="";
				  
        		$src = array();
        		preg_match( "#src=\"(.*?)\"#si", $inline_params, $src );
        		if (isset($src[1])) $src = trim($src[1]);
				  else $src = "";

        		$thetitle = array();
        		preg_match( "#title=\"(.*?)\"#si", $inline_params, $thetitle );
        		if (isset($thetitle[1])) $thetitle = trim($thetitle[1]);
				  else $thetitle = "";
				  
        		$alt = array();
        		preg_match( "#alt=\"(.*?)\"#si", $inline_params, $alt );
        		if (isset($alt[1])) $alt = trim($alt[1]);
				  else $alt = "";
				
				//if (stristr($src,"http://"))
				//	continue;
				
				  
// echo "==================== ".$urlbase . " ======================";
				$onsite=0;

				$urlbasecurr = str_replace('http://','',str_replace('http://www.','',JURI::base()));
				$srccurr = strtolower($src);
				if (!(strpos($srccurr, 'http://'.$urlbasecurr)===false)) {
					$urlbase = 'http://'.$urlbasecurr;
					$onsite=1;
				}
				elseif (!(strpos($srccurr, 'http://www.'.$urlbasecurr)===false)) {
					$urlbase = 'http://www.'.$urlbasecurr;
					$onsite=1;
				}
				else {
					preg_match( "#http://.*?/#si", $srccurr , $urlbase );
					if (isset($urlbase[0])) $urlbase = trim($urlbase[0]);
					  else $urlbase = "";				
				}
				$link = JString::str_ireplace($urlbase,"", $srccurr);
				
				//convert international domain name to punycode
				$IDN = new idna_convert();
				if ($urlbase)
					$urlbase = $IDN->encode($urlbase);

// echo "==================== ".$link . " ======================";

				// Prevent thumbs of thumbs
				if ( strpos( $link, $thumb_ext ) )	  
					continue;
					
				// Remove first slash for related url /images/stoties/image.jpg
				$firsts = substr($link,0,1);
				if ($firsts == "/" || $firsts == "\\")
					$link = substr($link,1);

				//Check remote pic or local					
				if (!function_exists('glob'))
					$tmp = @safe_glob(trim($link));
				else
					$tmp = @glob(trim($link));
					
//var_dump($tmp);
//echo $urlbase;
		
				if (count($tmp) < 1 && $urlbase) {
						$onsite=0;
						$link = $urlbase . $link;
						// if remote url then ignore image
						//continue;
				}
				if (!$ignoreindividual || strpos( $matches_img[0][$i], 'smartresizeindividual' ) ) {
					if (!$is_blog  && ($individ_width || $individ_height)) { // this is article or other
				    	$athwidth = $individ_width;
				    	$athheight = $individ_height;
					} elseif ($is_blog  && ($individ_blogwidth || $individ_blogheight)) {
				    	$athwidth = $individ_blogwidth;
				    	$athheight = $individ_blogheight;
					}
				}
				
				$thumbprefix = $athwidth . '_' . $athheight;
				$calcthumb_width = (int)$athwidth;
				$calcthumb_height = (int)$athheight;
				
				list($image_width,$image_height)=getimagesize($link);
				// echo $link . " : " .  $image_width . " : " . $image_height;
				
	        	$extension = substr($link,strrpos($link,"."));
					
				$isimage = ($extension == '.jpg' || $extension == '.jpeg' || $extension == '.png' || $extension == '.gif' ||
					    $extension == '.JPG' || $extension == '.JPEG' || $extension == '.PNG' || $extension == '.GIF');
				if (!$isimage || $image_width==0 || $image_height==0)
					  continue;

				$thesize = "[" . $image_width . " " . $image_height . "]";
				if ($calcthumb_width  && !$calcthumb_height) {
					$calcthumb_height = round($calcthumb_width * ($image_height/$image_width));
				} else
				if (!$calcthumb_width  && $calcthumb_height) {
					$calcthumb_width = round($calcthumb_height * ($image_width/$image_height));
				}
				
				$text = '';
				
				if ( strpos( $link, $aththumb_ext ) === false && ($image_width > $calcthumb_width || $image_height > $calcthumb_height) ) {
					$uri =& JURI::getInstance($link);
					$relpath = $uri->toString(array('path'));
					$image_name = substr($relpath,0,strrpos($relpath, "."));
					$a=strrpos($image_name,"/");
					$b=strrpos($image_name,"\\");
					if ($a>$b) $b=$a;

		        	$just_name = substr($image_name,$b+1);
					$just_path = substr($image_name,0,$b);
					
					if ($storethumb == 2) {
							$newpath = $just_path . DS . $thumb_subfolder_name;
							if (!is_dir(JPATH_ROOT . DS . $newpath)) {
								if (!mkdir(JPATH_ROOT . DS . $newpath,0755)) {
									 $storethumb = 0;
								}
							}
					}
					
					if ($onsite) {
						$full_path = JPATH_ROOT . DS . $relpath;
						if ($storethumb == 1) {
							$aththumb_ext_img = filectime($full_path) . $aththumb_ext;
							$thumb_path = JPATH_ROOT . DS . "cache" . DS . $just_name . $aththumb_ext_img . $thumbprefix . $extension;
							$thethumb = JURI::base() .  "cache" . "/" . $just_name .  $aththumb_ext_img . $thumbprefix . $extension;
						} elseif ($storethumb == 2) {

							$thumb_path = JPATH_ROOT . DS . $newpath . DS . $just_name . $aththumb_ext . $thumbprefix . $extension;
							$thethumb = JURI::base() . $just_path . "/" . $thumb_subfolder_name . "/" . $just_name .  $aththumb_ext . $thumbprefix . $extension;						
														
						} else {
							$thumb_path = JPATH_ROOT . DS . $image_name . $aththumb_ext . $thumbprefix . $extension;
							$thethumb = JURI::base() . $image_name .  $aththumb_ext . $thumbprefix . $extension;
						}
					} else {

			        	$full_path = $link;
											
						if ($storethumb == 1) {
							$reparr = array("\\","/",'http:',".");
							$aththumb_ext_img = str_replace($reparr,"",$urlbase) . $aththumb_ext;
							$thumb_path = JPATH_ROOT . DS . "cache" . DS . $just_name . $aththumb_ext_img . $thumbprefix . $extension;
							$thethumb = JURI::base() .  "cache" . "/" . $just_name .  $aththumb_ext_img . $thumbprefix . $extension;
						
						} elseif ($storethumb == 2) {

							$thumb_path = JPATH_ROOT . DS . $newpath . DS . $just_name . $aththumb_ext . $thumbprefix . $extension;
							$thethumb = JURI::base() . $just_path . "/" . $thumb_subfolder_name . "/" . $just_name .  $aththumb_ext . $thumbprefix . $extension;						
							
						} else {
							$thumb_path = JPATH_ROOT . DS . $image_name . $aththumb_ext . $thumbprefix . $extension;
							$thethumb = JURI::base() . $image_name .  $aththumb_ext . $thumbprefix . $extension;
						}
					}
					
//echo $full_path. ' : '. $thumb_path . ' : '. $thethumb;
					
					$thesize = "[" . $image_width . " " . $image_height . "]";

					if (!file_exists($thumb_path)) {
		        		$rd = new smartimgRedim(true, $improve_thumbnails, JPATH_CACHE);
		        		$rd->loadImage($full_path);
		        		$rd->redimToSize($calcthumb_width, $calcthumb_height, true);
		        		$rd->saveImage($thumb_path, $thumb_quality);
					}
//echo $link. ' : '.$matches_img[0][$i];

					// add style for image after <img tag
					
					// replace image file name
					$text = str_replace($src, $thethumb, $matches_img[0][$i]);
//echo $text;
					//$text = str_replace("smartresize", "nosmartresize", $text);
					$text = preg_replace( "#width=\".*?\"#si", "", $text );
					$text = preg_replace( "#height=\".*?\"#si", "", $text );
					$text = preg_replace( "#[\s\;\"]width:(.*?)px*[\s\;\"]#si", "", $text );
					$text = preg_replace( "#[\s\;\"]height:(.*?)px*[\s\;\"]#si", "", $text );
				
					if ($alt && $thetitle) $thetitle = $thetitle . ' :: '. $alt;
						else if ($alt) $thetitle = $alt;

					if (!$is_blog) {
						if ($openstyle == 1) {
							$doc =& JFactory::getDocument();
							$doc->addScript( "plugins/content/smartresizer/js/multithumb.js" );
							if (!stristr($link,"http://")) 
								$link = JURI::base().$link;
							$text = '<a href="javascript:void(0)" onclick = "smartthumbwindow(\''.$link.'\',\''.$alt.'\','.$image_width.','.$image_height.',0,0);" title="' . $thetitle . '">'.$text.'</a>';
						}
						else
							$text = '<a target="_blank" href="' . $link . '" rel="' . $compatibility . $thesize . '" title="' . $thetitle . '">'.$text.'</a>';
					}
					else if ($readmorelink) {
						if(version_compare(JVERSION,'1.6.0','<'))
 							$link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
						else
							$link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid));
						$text = '<a href="' . $link . '" title="' . $thetitle . '">'.$text.'</a>';
					}
					if ($imgstyle) {
						if (strrpos($imgstyle,';') === (strlen($imgstyle)-1)) $adds = ""; else $adds = ";";
						$instext = ' style="'.$imgstyle.$adds.$astyle.'"';
//echo "style origin:".$styleorigin. " ;"."adds: ".$adds. " ;"."instext: ".$instext."<br>"; 
						if ($styleorigin)
							$text = str_replace($styleorigin, $instext, $text);
						else {
							$text = preg_replace( "#<[\s\v]*img#si", "<img ".$instext, $text );
						}
					}					
					$row->text = str_replace( $matches_img[0][$i], $text, $row->text );
				}
			}
		}
    }
}

?>