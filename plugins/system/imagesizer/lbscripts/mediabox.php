<?PHP
/*------------------------------------------------------------------------
# mediabox.php for PLG - SYSTEM - IMAGESIZER
# ------------------------------------------------------------------------
# author    reDim - Norbert Bayer
# copyright (C) 2011 redim.de. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.redim.de
# Technical Support:  Forum - http://www.redim.de/kontakt/
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::_('behavior.mootools');

$path="plugins"."/"."system"."/"."imagesizer"."/"."lbscripts"."/"."mediabox"."/";

$lang =& JFactory::getLanguage();
$l=substr($lang->getTag(),0,2);
$document   =& JFactory::getDocument();

if(file_exists(JPATH_SITE."/".$path.$l.'_mediaboxAdvBlack.css')){
	$document->addStyleSheet($path.$l.'_mediaboxAdvBlack.css','text/css',"screen");	
}else{
	$document->addStyleSheet($path.'mediaboxAdvBlack.css','text/css',"screen");
}

if(file_exists(JPATH_SITE.DS.$path.$l.'mediaboxAdv.js')){
	$document->addScript($path.$l.'mediaboxAdv.js');
}else{
	$document->addScript($path.'mediaboxAdv.js');	
}
unset($path);


function ImageSizer_addon_GetImageHTML(&$ar,&$img,&$imagesizer){

	$output=plgSystemimagesizer::make_img_output($ar);

	$x=explode("/",$ar["href"]);
	$c=count($x)-1;
	$x[$c]=rawurlencode($x[$c]);
	$x=implode("/",$x);
	
	$title="";
	if(!empty($ar["title"])){
		$title.=$ar["title"];
	}
	if(!empty($ar["alt"])){
	 	if($title!=""){$title.="::";}
		$title.=$ar["alt"];
	}

	$id=0;
	
	if(isset($imagesizer->article->id)){
		$id=$imagesizer->article->id;
	}

	$output='<a class="'.trim($imagesizer->params->get("linkclass","linkthumb")).'" target="_blank" title="'.$title.'" rel="lightbox[id'.$id.']" href="'.$x.'"><img '.$output.' /></a>';	

	return $output;

}
