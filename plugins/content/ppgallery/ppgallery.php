<?php

/** pPGallery a Joomla! 1.6+ content plugin
* @version: ppgallery.php -> 4.313
* @package: plgPPGallery
* @copyright: This plugin is released under the GNU/GPL License.
* @license: GNU General Public License version 2 or later.
* Author: C.Schneider
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.event.plugin');
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
class plgContentPPGallery extends JPlugin {

function onContentPrepare($context, &$article, &$params, $page=0) {

  //error_reporting(E_ALL);
  // or:
  //ini_set ('error_reporting', E_ALL);

  global $mainframe;

  //plgString to be used e.g. for existing galleries
  $plgstring = $this->params->get('plgstring', 'ppgallery'); 

  //regular expression search string
  $regexp = "~{".$plgstring."(?:\s?(.*?)?)?}(.*?){/".$plgstring."}~is";
  if (!preg_match($regexp, $article->text)) {
    return;
  }

// Walkaround for J1.6+ due to no parsing of article id etc.,
  // If not in the article view, we have to get the full $article object ourselves
	// We only want to handle com_content items; if this function returns null, there's no DB item
	// Also, make sure the object isn't already loaded and undo previous plugin processing
	$view	= JRequest::getCmd('view');
  if ($view == 'featured' || $view == 'category') {
	  if ((!is_null($this->_loadArticle($article))) && (!isset($article->catid))) {
		$article = $this->_loadArticle($article);
		  if (!isset($article->text)) {
			  $article->text = $article->introtext;
			}
	  }
  }
	if (is_null($article->id)) {
	  $articleId = 0;
	}
	else {
		$articleId = $article->id;
	}
// End walkaround plus _loadArticle

  $matches = array();
  if (preg_match_all($regexp, $article->text, $matches, PREG_PATTERN_ORDER) > 0) {
    $doc =& JFactory::getDocument();
    $relpath = JURI::base(true);  // relative path
    $abspath = JPATH_SITE;        //absolute path to J! install path
    $imgroot = JComponentHelper::getParams('com_media')->get('image_path');  //images root from J! Media options for *folders*
	//$imgroot = '../coppermine/albums/Jak95';//JComponentHelper::getParams('com_media')->get('image_path');  //images root from J! Media options for *folders*

	
//require_once('./FirePHPCore/FirePHP.class.php');

//ob_start();

//require_once('./FirePHPCore/fb.php');

//$firephp = FirePHP::getInstance(true);
//$firephp->log('imgroot', $imgroot);	
	
	
    //prettyPhoto script init. for defaults and gloal parameter settings
    if (!defined('onlyonce')){
      $ppparams = '';
      if (($ppani = $this->params->get('ppAni', 'normal')) != 'fast') $ppparams = ',animation_speed:"'.$ppani.'"'; 
      if (($ppopac = $this->params->get('ppOpac', 0.80)) != '0.80') $ppparams .= ',opacity:'.$ppopac.'';
      if (($ppslide = $this->params->get('ppSlide', '')) != '') $ppparams .= ',slideshow:'.$ppslide.'';
      if ($ppslide != '') if ($ppslideauto = $this->params->get('ppSlideAuto', false)) $ppparams .= ',autoplay_slideshow:true';
      if (!$ppthumbs = $this->params->get('ppThumbs', true)) $ppparams .= ',overlay_gallery:false';
      if (!$pptitle = $this->params->get('ppTitle', true)) $ppparams .= ',show_title:false';
      if (!$ppresize = $this->params->get('ppResize', true)) $ppparams .= ',allow_resize:false';
      if ($pphidef = $this->params->get('ppHidef', false)) $ppparams .= ',hideflash:true';
      if (($ppsep = $this->params->get('ppSep', '/')) != '/') $ppparams .= ',counter_separator_label:" '.$ppsep.'"';
      if ($ppmodal = $this->params->get('ppModal', false)) $ppparams .= ',modal:true';
      if (!$ppDeeplnk = $this->params->get('ppDeeplnk', true)) $ppparams .= ',deeplinking:false';
      $ppparams .= ',social_tools:false';
      $pptheme = $this->params->get('ppTheme', 'default'); $pptheme == 'default' ? $ppparams .= ',theme:"pp_default"' : $ppparams .= ',theme:"'.$pptheme.'"';
      $noconflict = $this->params->get('noconflict', 1);
      $loadjq = $this->params->get('loadjq', 1);

      if ($noconflict) { $doc->addScriptDeclaration('      jQuery.noConflict();'); }
      $doc->addScriptDeclaration('      jQuery(document).ready(function($) {$("a[rel^=\"prettyPhoto\"]").prettyPhoto({'.ltrim($ppparams,",").'})}); //v.4.312');
      define('onlyonce', true);
    } 
    //for each found pPGallery
    $ppgallery = -1;
    foreach ($matches[0] as $match) { //[0] whole str, [1] params, [2] path or imgs
      $ppgallery++;
      $override = $matches[1][$ppgallery]; //1st!
      //Get backend parameter settings
      $twidth = plgContentPPGalleryGetOverrides($override, 'width', $this->params->get('width', 200));
      $theight= plgContentPPGalleryGetOverrides($override, 'height', $this->params->get('height', 200));
      $tcrop= plgContentPPGalleryGetOverrides($override, 'crop', $this->params->get('crop', 0));
      $fixed_w = plgContentPPGalleryGetOverrides($override, 'fixed_w', $this->params->get('fixed_w', '1'));
      $talign = plgContentPPGalleryGetOverrides($override, 'valign', $this->params->get('valign', 'top'));
      $padd_v = plgContentPPGalleryGetOverrides($override, 'padding_v', $this->params->get('padd_v', 5));
      $padd_h = plgContentPPGalleryGetOverrides($override, 'padding_h', $this->params->get('padd_h', 5));
      $quality_jpg= plgContentPPGalleryGetOverrides($override, 'quality_j', $this->params->get('quality_jpg', 75));
      $quality_png= plgContentPPGalleryGetOverrides($override, 'quality_p', $this->params->get('quality_png', 6));
      $logo = plgContentPPGalleryGetOverrides($override, 'logo', $this->params->get('logo', '-1'));
      $logo_pos = plgContentPPGalleryGetOverrides($override, 'logo_pos', $this->params->get('logo_pos', 9));
      $caption = plgContentPPGalleryGetOverrides($override, 'caption', $this->params->get('caption', 'none'));
      $thbs_limit = plgContentPPGalleryGetOverrides($override, 'limit', $this->params->get('t_limit', ''));
      $thbs_only = plgContentPPGalleryGetOverrides($override, 't_only', $this->params->get('t_only', 0));
      $pre_txt = plgContentPPGalleryGetOverrides($override, 'prefix_txt', $this->params->get('pre_txt', ''));
      $lnk_pop = plgContentPPGalleryGetOverrides($override, 'link_popup', $this->params->get('lnk_pop', ''));
      $subfolders = plgContentPPGalleryGetOverrides($override, 'subfolders', $this->params->get('subfolders', '0'));
      $filter = plgContentPPGalleryGetOverrides($override, 'filter', $this->params->get('filter', ''));
      $layout = $this->params->get('layout', 'default'); //no override, use css class sfx
      $cssclass_sfx = plgContentPPGalleryGetOverrides($override, 'css_class', $this->params->get('cssclass_sfx', ''));
	    if ($cssclass_sfx == '' && $layout != 'default') $cssclass_sfx = $layout;
      $csvfile = plgContentPPGalleryGetOverrides($override, 'csvfile', $this->params->get('csvfile', 'ppgallery.txt'));
      (($customid = plgContentPPGalleryGetOverrides($override, 'id', '')) != '' ? $ppgid = $customid : $ppgid = $articleId.$ppgallery);
      $content = "";
      // 0 = no thumbs only script for pP will be loaded 
      if ($thbs_limit != '0') {

        //I M G Mode: loads imgs from content <img>-tags
        if (preg_match_all('(<img.*?>)', $matches[2][$ppgallery], $img_tag) != 0) {
          $img_tag = $img_tag[0];
          //check and replace non existing imgs
          unset($img_chk);
          foreach ($img_tag as $chk) {
            $chk_file = plgContentPPGalleryGetAttr($chk, 'src');
			//$firephp->log('chk_file', $chk_file);	
            if (file_exists($chk_file)) {
			  //$firephp->log('chk', $chk);	
              $img_chk[] = $chk;
            }
			  //if (isset($img_chk)) {
				$img_tag = $img_chk;
			  //}
          }
          unset($imageset);
          $i = 0;
          foreach ($img_tag as $img) {
            $imageset[$i] -> img_path = str_replace('\\','/',(JPath::clean(dirname(plgContentPPGalleryGetAttr($img, 'src')))));
            $imageset[$i] -> img_file = JFile::getName(JPath::clean(plgContentPPGalleryGetAttr($img, 'src'))); //basename
            $imageset[$i] -> img_alt = plgContentPPGalleryGetAttr($img, 'alt');
            $imageset[$i] -> img_title = plgContentPPGalleryGetAttr($img, 'title');
			$imageset[$i] -> img_album = plgContentPPGalleryGetAttr($img, 'album');
			$imageset[$i] -> img_pid = plgContentPPGalleryGetAttr($img, 'pid');
            $i++ ;
          }
          $imgcount = count($imageset);
        } else {

          //F o l d e r-Mode: loads all imgs from folder into array
          $imgpath  = $abspath.DS.$imgroot.DS.$matches[2][$ppgallery];
          unset($imageset);
          !$filter ? $filter = '(?i)(.jpg$|.gif$|.png$)' : $filter;
          $img_files = JFolder::files($imgpath, $filter, $subfolders, true); 
          array_multisort($img_files, SORT_ASC, SORT_REGULAR);
          foreach ($img_files as $k => $pathfile) {
            $ifile = JFile::getName($pathfile); //basename
            $ipath = ltrim(substr(dirname($pathfile),(strlen($abspath))),DS);
            $imageset[$k] -> img_path = str_replace('\\','/',$ipath); 
            $imageset[$k] -> img_file = $ifile; 
            $imageset[$k] -> img_alt = plgContentPPGalleryGetCsv($ifile, $ipath.DS.$csvfile, 'alt');
            $imageset[$k] -> img_title = plgContentPPGalleryGetCsv($ifile, $ipath.DS.$csvfile, 'title');
          }
          $imgcount = count($imageset); 
        }

        //content output...
        if($imgcount) {
          $content = "<span class=\"ppgallery".$cssclass_sfx."\">\n";
          if ($thbs_limit != '') {
            $imgs_hidden = $imgcount;
            if ($imgcount > $thbs_limit) {
              $imgcount = $thbs_limit;
            }
          }
          for($a = 0; $a < $imgcount; $a++) {
            if($imageset[$a]->img_file != '') {
            //thumbs-size calculation, 0=w, 1=h
            $imagedata = getimagesize($imageset[$a]->img_path.DS.$imageset[$a]->img_file);
            $thb_w = $twidth;
            if ($tcrop) {
              $thb_h = $twidth; $theight = $twidth;                   //square: height = width
            } else {
              $thb_h = (int)($imagedata[1]*($thb_w/$imagedata[0]));   //all have the same width (diff. heights)
              if(($theight) AND ($thb_h > $theight)) {
                $thb_h = $theight;
                $thb_w = (int)($imagedata[0]*($thb_h/$imagedata[1])); //all have the same height (diff. width)
              }
            }
            //create/check thumb folder in J!cache
            if (!JFolder::exists(JPATH_CACHE.DS.'ppgallery'.DS.$ppgid.DS.$imageset[$a]->img_path)) {
              JFolder::create(JPATH_CACHE.DS.'ppgallery'.DS.$ppgid.DS.$imageset[$a]->img_path, 0755);
            }
            // define thb-file and logo-file name
            if ($logo == "") {
              $logo_file = ""; $logo_pos = "";
            } else {
              $logo_file = JPath::clean($logo); // jpath clean? necessary?
            }
            if (substr(strtolower($imageset[$a]->img_file),-3) == 'jpg') {
              $thb_file = 'cache/ppgallery/'.$ppgid."/".$imageset[$a]->img_path."/".$thb_w."x".$thb_h."_q".$quality_jpg.JFile::stripExt(JFile::getName($logo_file)).$logo_pos."_t_".$imageset[$a]->img_file;
            }
            if (substr(strtolower($imageset[$a]->img_file),-3) == 'png') {
              $thb_file = 'cache/ppgallery/'.$ppgid."/".$imageset[$a]->img_path."/".$thb_w."x".$thb_h."_q".$quality_png.JFile::stripExt(JFile::getName($logo_file)).$logo_pos."_t_".$imageset[$a]->img_file;
            }
            if (substr(strtolower($imageset[$a]->img_file),-3) == 'gif') {
              $thb_file = 'cache/ppgallery/'.$ppgid."/".$imageset[$a]->img_path."/".$thb_w."x".$thb_h.JFile::stripExt(JFile::getName($logo_file)).$logo_pos."_t_".$imageset[$a]->img_file;
            }
            // thumb vertical/horizontal alignment + caption top (+4 from css file for drop-shadow!)
            $content .= '<span class="ppg_thbox'.$ppgid.'">';
            if ($talign == 'top') {
              if ($caption == 'top') {$content .= '<span class="ppg_captop'.$cssclass_sfx.'">'.html_entity_decode($imageset[$a]->img_alt).'</span>';}
              ($fixed_w ? $content .= '<span class="ppg_thb'.$cssclass_sfx.'" style="margin-left: '.((($twidth-$thb_w)/2)+4).'px;">' : $content .= '<span class="ppg_thb'.$cssclass_sfx.'">');
            }
            if ($talign == 'middle') {
              if ($caption == 'top') {$content .= '<span class="ppg_captop'.$cssclass_sfx.'" style="position: relative; top: '.(($theight-$thb_h)/2).'px">'.html_entity_decode($imageset[$a]->img_alt).'</span>';}
              ($fixed_w ? $content .= '<span class="ppg_thb'.$cssclass_sfx.'" style="margin-left: '.((($twidth-$thb_w)/2)+4).'px; margin-top: '.((($theight-$thb_h)/2)+4).'px;">' : $content .= '<span class="ppg_thb'.$cssclass_sfx.'" style="margin-top: '.((($theight-$thb_h)/2)+4).'px;">'); 
            }
            if ($talign == 'bottom') {
              if ($caption == 'top') {$content .= '<span class="ppg_captop'.$cssclass_sfx.'" style="position: relative; top: '.($theight-$thb_h).'px">'.html_entity_decode($imageset[$a]->img_alt).'</span>';}
              ($fixed_w ? $content .= '<span class="ppg_thb'.$cssclass_sfx.'" style="margin-left: '.((($twidth-$thb_w)/2)+4).'px; margin-top: '.(($theight-$thb_h)+4).'px;">' : $content .= '<span class="ppg_thb'.$cssclass_sfx.'" style="margin-top: '.(($theight-$thb_h)+4).'px;">'); 
            }
            // a H R E F  tag
            if (!$thbs_only) {
              if (!empty($imageset[$a]->img_album)) {
				if (!empty($imageset[$a]->img_pid)) {
					$content .= '<a href="http://www.jak95.ru/coppermine/displayimage.php?album='.$imageset[$a]->img_album.'&pid='.$imageset[$a]->img_pid.'#top_display_media'."/";
				} else {
					$content .= '<a href="http://www.jak95.ru/coppermine/thumbnails.php?album='.$imageset[$a]->img_album.'#top_display_media'."/";
				}
				//$firephp->log('$content', $content);	
			  } else {
				$content .= '<a href="'.$relpath."/".$imageset[$a]->img_path."/".$imageset[$a]->img_file;
			  }
			  $content .= '" rel="prettyPhoto['.$ppgid.']" title="';
              if ($pre_txt) {$content .= $pre_txt.'<br /><br />';}
              $content .= $imageset[$a]->img_title.'" target="_blank">';
            }
            // if thb with desired settings already exists, proceed with output, otherwise build/rebuild thb
            if(file_exists($thb_file)){
            } else {
              list($img_w, $img_h) = getimagesize($imageset[$a]->img_path.DS.$imageset[$a]->img_file);

              //load thumbs from cache folder into array
              $thumbs = array();
              $thb_files  = JFolder::files(($abspath.DS.'cache'.DS.'ppgallery'.DS.$ppgid.DS.$imageset[$a]->img_path), '(?i)(.jpg$|.gif$|.png$)', false, true);
              foreach ($thb_files as $tk => $tpathfile) {
                $tfile = JFile::getName($tpathfile); //basename
                $thumbs[$tk] -> img = substr($tfile, strpos($tfile,'_t_')+3);
                $thumbs[$tk] -> thb = $tfile;
              }

              //search for a thb (w/o the settings) in (thbs array) with existing img_name and delete it
              $tfound = plgContentPPGallerySearchMultiArray($thumbs, 'img', $imageset[$a]->img_file);
              //if a thb exists but with different settings -> delete before rebuilding a new one
              if ($tfound != -1) {
                if (is_file($abspath.DS.'cache'.DS.'ppgallery'.DS.$ppgid.DS.$imageset[$a]->img_path.DS.$thumbs[$tfound]->thb)) {
                  unlink($abspath.DS.'cache'.DS.'ppgallery'.DS.$ppgid.DS.$imageset[$a]->img_path.DS.$thumbs[$tfound]->thb );
                }
              }
              //generate the thbs
              if (substr(strtolower($thb_file),-3) == 'gif') {
                $img = ImageCreateFromGIF($imageset[$a]->img_path.DS.$imageset[$a]->img_file);
                $thb = ImageCreateTrueColor($thb_w, $thb_h);
                if ($tcrop) {
                  if ($img_w > $img_h) {
                    $off_w = ($img_w-$img_h)/2; $off_h = 0; $img_w = $img_h;
                  } elseif ($img_h > $img_w) {
                    $off_w = 0; $off_h = ($img_h-$img_w)/2; $img_h = $img_w;
                  } else { $off_w = 0; $off_h = 0; }
                }
                else { $off_w = 0; $off_h = 0; }
                imagecopyresampled($thb, $img, 0, 0, $off_w, $off_h, $thb_w, $thb_h, $img_w, $img_h);
                ImageGIF($thb, $thb_file);
                if ($logo != "") {
                  $thb = imageCreateFromGIF($thb_file);
                  $log = imageCreateFromPNG($logo_file);
                  $log_w = imageSX($log); $log_h = imageSY($log);
                  if ($logo_pos == 1) { $thb_x = 0; $thb_y = 0; }
                  if ($logo_pos == 2) { $thb_x = (($thb_w - $log_w)/2); $thb_y = 0; }
                  if ($logo_pos == 3) { $thb_x = $thb_w - $log_w; $thb_y = 0; }
                  if ($logo_pos == 4) { $thb_x = 0; $thb_y = ($thb_h/2) - ($log_h/2); }
                  if ($logo_pos == 5) { $thb_x = ($thb_w/2) - ($log_w/2); $thb_y = ($thb_h/2) - ($log_h/2); }
                  if ($logo_pos == 6) { $thb_x = $thb_w - $log_w; $thb_y = ($thb_h/2) - ($log_h/2); }
                  if ($logo_pos == 7) { $thb_x = 0; $thb_y = $thb_h - $log_h; }
                  if ($logo_pos == 8) { $thb_x = (($thb_w - $log_w)/2); $thb_y = $thb_h - $log_h; }
                  if ($logo_pos == 9) { $thb_x = $thb_w - $log_w; $thb_y = $thb_h - $log_h; }
                  imageCopy($thb, $log, $thb_x, $thb_y, 0, 0, $log_w, $log_h);
                  imageGIF($thb, $thb_file);
                }
              }
              elseif (substr(strtolower($thb_file),-3) == 'jpg') {
                $img = ImageCreateFromJPEG($imageset[$a]->img_path.DS.$imageset[$a]->img_file);
                $thb = ImageCreateTrueColor($thb_w, $thb_h);
                if ($tcrop) {
                  if ($img_w > $img_h) {
                    $off_w = ($img_w-$img_h)/2; $off_h = 0; $img_w = $img_h;
                  } elseif ($img_h > $img_w) {
                    $off_w = 0; $off_h = ($img_h-$img_w)/2; $img_h = $img_w;
                  } else { $off_w = 0; $off_h = 0; }
                }
                else { $off_w = 0; $off_h = 0; }
                imagecopyresampled($thb, $img, 0, 0, $off_w, $off_h, $thb_w, $thb_h, $img_w, $img_h);
                ImageJPEG($thb, $thb_file, $quality_jpg);
                if ($logo != "") {
                  $thb = imageCreateFromJPEG($thb_file);
                  $log = imageCreateFromPNG($logo_file);
                  $log_w = imageSX($log); $log_h = imageSY($log);
                  if ($logo_pos == 1) { $thb_x = 0; $thb_y = 0; }
                  if ($logo_pos == 2) { $thb_x = (($thb_w - $log_w)/2); $thb_y = 0; }
                  if ($logo_pos == 3) { $thb_x = $thb_w - $log_w; $thb_y = 0; }
                  if ($logo_pos == 4) { $thb_x = 0; $thb_y = ($thb_h/2) - ($log_h/2); }
                  if ($logo_pos == 5) { $thb_x = ($thb_w/2) - ($log_w/2); $thb_y = ($thb_h/2) - ($log_h/2); }
                  if ($logo_pos == 6) { $thb_x = $thb_w - $log_w; $thb_y = ($thb_h/2) - ($log_h/2); }
                  if ($logo_pos == 7) { $thb_x = 0; $thb_y = $thb_h - $log_h; }
                  if ($logo_pos == 8) { $thb_x = (($thb_w - $log_w)/2); $thb_y = $thb_h - $log_h; }
                  if ($logo_pos == 9) { $thb_x = $thb_w - $log_w; $thb_y = $thb_h - $log_h; }
                  imageCopy($thb, $log, $thb_x, $thb_y, 0, 0, $log_w, $log_h);
                  imageJPEG($thb, $thb_file, $quality_jpg);
                }
              }
              elseif (substr(strtolower($thb_file),-3) == 'png') {
                $img = ImageCreateFromPNG($imageset[$a]->img_path.DS.$imageset[$a]->img_file);
                $thb = ImageCreateTrueColor($thb_w, $thb_h);
                if ($tcrop) {
                  if ($img_w > $img_h) {
                    $off_w = ($img_w-$img_h)/2; $off_h = 0; $img_w = $img_h;
                  } elseif ($img_h > $img_w) {
                    $off_w = 0; $off_h = ($img_h-$img_w)/2; $img_h = $img_w;
                  } else { $off_w = 0; $off_h = 0; }
                }
                else { $off_w = 0; $off_h = 0; }
                imagecopyresampled($thb, $img, 0, 0, $off_w, $off_h, $thb_w, $thb_h, $img_w, $img_h);
                ImagePNG($thb, $thb_file, $quality_png);
                if ($logo != "") {
                  $thb = imageCreateFromPNG($thb_file);
                  $log = imageCreateFromPNG($logo_file);
                  $log_w = imageSX($log); $log_h = imageSY($log);
                  if ($logo_pos == 1) { $thb_x = 0; $thb_y = 0; }
                  if ($logo_pos == 2) { $thb_x = (($thb_w - $log_w)/2); $thb_y = 0; }
                  if ($logo_pos == 3) { $thb_x = $thb_w - $log_w; $thb_y = 0; }
                  if ($logo_pos == 4) { $thb_x = 0; $thb_y = ($thb_h/2) - ($log_h/2); }
                  if ($logo_pos == 5) { $thb_x = ($thb_w/2) - ($log_w/2); $thb_y = ($thb_h/2) - ($log_h/2); }
                  if ($logo_pos == 6) { $thb_x = $thb_w - $log_w; $thb_y = ($thb_h/2) - ($log_h/2); }
                  if ($logo_pos == 7) { $thb_x = 0; $thb_y = $thb_h - $log_h; }
                  if ($logo_pos == 8) { $thb_x = (($thb_w - $log_w)/2); $thb_y = $thb_h - $log_h; }
                  if ($logo_pos == 9) { $thb_x = $thb_w - $log_w; $thb_y = $thb_h - $log_h; }
                  imageCopy($thb, $log, $thb_x, $thb_y, 0, 0, $log_w, $log_h);
                  imagePNG($thb, $thb_file, $quality_png);
                }
              }
            }
            // I M G  / S R C tag - - -
            if ($caption == 'label'  && $imageset[$a]->img_alt != "") {$content .= '<span class="ppg_caplbl'.$cssclass_sfx.'">'.html_entity_decode($imageset[$a]->img_alt).'</span>';} // caption label
            $content .= '<img src="'.$relpath."/".$thb_file.'" alt="'.($imageset[$a]->img_alt == "" ? $imageset[$a]->img_file : $imageset[$a]->img_alt).'" width="'.$thb_w.'" height="'.$thb_h.'" title="';
            if (!$thbs_only && $lnk_pop) {
              $content .= $lnk_pop.'" />'; }
            else {
             ($imageset[$a]->img_title == "" ? $content .= $imageset[$a]->img_file.'" />' : $content .= $imageset[$a]->img_title.'" />');
            }
            if (!$thbs_only) {$content .= '</a>';}
            $content .= '</span>';
            if ($caption == 'bottom') {$content .= '<span class="ppg_capbot'.$cssclass_sfx.'">'.html_entity_decode($imageset[$a]->img_alt).'</span>';} // caption bottom
            $content .= "</span>\n";
            }
          }
          // hidden a H R E F
          $content .="<span class=\"ppg_clr\"></span>\n</span>\n";
          if (($thbs_limit != '') && ($thbs_limit < $imgs_hidden)) {
            for($a = $thbs_limit; $a < $imgs_hidden; $a++) {
              if($imageset[$a]->img_file != '') {
                $content .= '<span style="display: none"><a href="'.$relpath."/".$imageset[$a]->img_path."/".$imageset[$a]->img_file.'" rel="prettyPhoto['.$ppgid.']" title="';
                if ($pre_txt) {$content .= $pre_txt.'<br /><br />';}
                $content .= $imageset[$a]->img_title.'" target="_blank">';
                $content .= '<img src="" alt="'.($imageset[$a]->img_alt == "" ? $imageset[$a]->img_file : $imageset[$a]->img_alt).'" />';
                $content .= "</a></span>\n";
              }
            }
          }
        }
        //head declarations
        // c s s
        $css_thbox = '      .ppg_thbox'.$ppgid.' { float: left; ';
        if ($caption == 'none' || $caption == 'label') { $css_thbox .= 'height: '.($theight+12).'px; margin: 0 '.$padd_h.'px '.$padd_v.'px 0;';}
        if ($caption == 'top') { $css_thbox .= 'height: '.($theight+30).'px; margin: 0 '.$padd_h.'px '.$padd_v.'px 0;';}
        if ($caption == 'bottom') { $css_thbox .= 'height: '.($theight+10).'px; margin: 1.5em '.$padd_h.'px '.$padd_v.'px 0;';}
        ($fixed_w ? $css_thbox .= ' width: '.($twidth+10).'px; }' : $css_thbox .= ' }');
        $doc->addStyleDeclaration($css_thbox);
      }
      //pP inline parameter overrides and settings for multiple instance with different settings
      unset($ppparams); $ppparams[$ppgid] = ',';
      if (($ppani = plgContentPPGalleryGetOverrides($override, 'ani', $this->params->get('ppAni', 'normal'))) != 'fast') $ppparams[$ppgid] = ',animation_Speed:"'.$ppani.'"';
      if (($ppopac = plgContentPPGalleryGetOverrides($override, 'opac', $this->params->get('ppOpac', 0.80))) != '0.80') $ppparams[$ppgid] .= ',opacity:'.$ppopac.'';
      if (($ppslide = plgContentPPGalleryGetOverrides($override, 'slide', $this->params->get('ppSlide', ''))) != '') $ppparams[$ppgid] .= ',slideshow:'.$ppslide.'';
      if ($ppslide != '') if ($ppslideauto = plgContentPPGalleryGetOverrides($override, 'slideauto', $this->params->get('ppSlideAuto', false))) $ppparams[$ppgid] .= ',autoplay_slideshow:true';
      if (!$ppthumbs = plgContentPPGalleryGetOverrides($override, 'thumbs', $this->params->get('ppThumbs', true))) $ppparams[$ppgid] .= ',overlay_gallery:false';
      if (!$pptitle = plgContentPPGalleryGetOverrides($override, 'title', $this->params->get('ppTitle', true))) $ppparams[$ppgid] .= ',show_title:false';
      if (!$ppresize = plgContentPPGalleryGetOverrides($override, 'resize', $this->params->get('ppResize', true))) $ppparams[$ppgid] .= ',allow_resize:false';
      if ($pphidef = plgContentPPGalleryGetOverrides($override, 'hideflash', $this->params->get('ppHidef', false))) $ppparams[$ppgid] .= ',hideflash:true';
      if (($ppsep = plgContentPPGalleryGetOverrides($override, 'separator', $this->params->get('ppSep', '/'))) != '/') $ppparams[$ppgid] .= ',counter_separator_label:" '.$ppsep.' "';
      if ($ppmodal = plgContentPPGalleryGetOverrides($override, 'modal', $this->params->get('ppModal', false))) $ppparams[$ppgid] .= ',modal:true';
      $pptheme = plgContentPPGalleryGetOverrides($override, 'theme', $this->params->get('ppTheme', 'default')); $pptheme == 'default' ? $ppparams[$ppgid] .= ',theme:"pp_default"' : $ppparams[$ppgid] .= ',theme:"'.$pptheme.'"';
      if (!$ppDeeplnk = plgContentPPGalleryGetOverrides($override, 'deeplink', $this->params->get('ppDeeplnk', true))) $ppparams[$ppgid] .= ',deeplinking:false';
      //overrides for stand-alone (thumbs=0 and 'id'):
      if (($ppdefw = plgContentPPGalleryGetOverrides($override, 'defwidth', 500)) != '500')  $ppparams[$ppgid] .= ',default_width:'.$ppdefw.'';
      if (($ppdefh = plgContentPPGalleryGetOverrides($override, 'defheight', 344)) != '344') $ppparams[$ppgid] .= ',default_height:'.$ppdefh.'';
      if (($ppwmode = plgContentPPGalleryGetOverrides($override, 'wmode', 'opaque')) != 'opaque') $ppparams[$ppgid] .= ',wmode:"'.$ppwmode.'"';
      if (!$ppvideoauto = plgContentPPGalleryGetOverrides($override, 'videoauto', true)) $ppparams[$ppgid] .= ',autoplay:false' ;
      if (!$ppkb = plgContentPPGalleryGetOverrides($override, 'keyboard', true)) $ppparams[$ppgid] .= ',keyboard_shortcuts:false';
      $ppparams[$ppgid] .= ',social_tools:false';

      $doc->addScriptDeclaration('      jQuery(document).ready(function($) {$("a[rel^=\"prettyPhoto['.$ppgid.']\"]").prettyPhoto({'.ltrim($ppparams[$ppgid],",").'}) });');

      //removes {.}...{/.} from content and replace with the gallery
      $article->text = str_replace($matches[0][$ppgallery], $content, $article->text);
    }
    $doc->addStyleSheet($relpath.'/plugins/content/ppgallery/ppgallery/res/prettyPhoto.css');
    $doc->addStyleSheet($relpath.'/plugins/content/ppgallery/ppgallery/res/pPGallery.css');
    if ($loadjq) {
      $doc->addScript($relpath.'/plugins/content/ppgallery/ppgallery/res/jquery.js" charset="utf-8');
      $doc->addScript($relpath.'/plugins/content/ppgallery/ppgallery/res/jquery.prettyPhoto.js" charset="utf-8');
    }
  }
}
private function _loadArticle($article) {
		// Query the database for the article text
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__content'));
		$query->where($db->quoteName('introtext').' = '.$db->quote($article->text));
		$db->setQuery($query);
		$article = $db->loadObject();
		return $article;
}
}
// - s u b - f u n c t i o n s - - -
function plgContentPPGalleryGetOverrides($override, $attribute, $default = null) {
  $matches = array();
  preg_match_all('/(\w+)(\s*=\s*\".*?\")/s', htmlspecialchars_decode($override), $matches); // html..decode: some editors repl spec.chars with entities;
  $count = count($matches[1]);
  for ($i = 0; $i < $count; $i++) {
    if (strtolower($matches[1][$i]) == strtolower($attribute)) {
      $value = ltrim($matches[2][$i], " \n\r\t=");
      $value = trim($value, '""');
      return $value; 
    }
  }
  return $default;
}

function plgContentPPGallerySearchMultiArray ($array, $index, $value) { //thumbs, img, filename
$arrkey = -1;
  if(is_array($array) && count($array)>0) { 
      foreach(array_keys($array) as $key) {
        $tempkey[$key] = $array[$key]->$index;
          if ($tempkey[$key] == $value) {
              $arrkey = $key;
         }
      }
  }
  return $arrkey;
}

function plgContentPPGalleryGetAttr($tag, $attr) {
  preg_match('#' . $attr . '\s?=\s?"(.*?)"#', $tag, $attr_value);
  if (isset($attr_value[1])) {
    if (strtolower($attr) == 'src') $attr_value[1] = ltrim($attr_value[1], DS); // remove a leading DS coming from some editors
    return $attr_value[1];
  }
  return '';
}

function plgContentPPGalleryGetCsv($i_file, $csv, $attr) {
  if(file_exists($csv)){
    $handle = fopen ($csv, "r"); 
    $attr_value = "";
    while (($data = fgetcsv ($handle, 1000, ",")) !== FALSE ) {
      if ($i_file == $data[0]) {
        ($attr == 'alt' ? $attr_value = $data[1] : $attr_value = $data[2]);
      }
    }
    fclose ($handle);
    return $attr_value;
  }
}


