<?PHP
/*------------------------------------------------------------------------
# slimbox.php for PLG - SYSTEM - IMAGESIZER
# ------------------------------------------------------------------------
# author    reDim - Norbert Bayer
# copyright (C) 2011 redim.de. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.redim.de
# Technical Support:  Forum - http://www.redim.de/kontakt/
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::_('behavior.mootools');

$path="plugins"."/"."system"."/"."imagesizer"."/"."lbscripts"."/"."slimbox"."/";

$lang =& JFactory::getLanguage();
$l=substr($lang->getTag(),0,2);
$document   =& JFactory::getDocument();

if(file_exists(JPATH_SITE.DS.$path.$l.'_slimbox.css')){
	$document->addStyleSheet($path.$l.'_slimbox.css','text/css',"screen");	
}else{
	$document->addStyleSheet($path.'slimbox.css','text/css',"screen");
}

if(file_exists(JPATH_SITE.DS.$path.$l.'_slimbox.js')){
	$document->addScript($path.$l.'_slimbox.js');
}else{
	$document->addScript($path.'slimbox.js');	
}
unset($path);


function ImageSizer_addon_GetImageHTML(&$ar,&$img,&$imagesizer){

	$output=plgSystemimagesizer::make_img_output($ar);

	$x=explode("/",$ar["href"]);
	$c=count($x)-1;
	$x[$c]=rawurlencode($x[$c]);
	$x=implode("/",$x);

	if(isset($ar["title"])){
		$title=' title="'.$ar["title"].'"';
	}else{
		$title="";
	} 
	
	$id=0;
	
	if(isset($imagesizer->article->id)){
		$id=$imagesizer->article->id;
	}
	
	$output='<a class="'.trim($imagesizer->params->get("linkclass","linkthumb")." modal").'" target="_blank"'.$title.' rel="lightbox[id_'.$id.']" href="'.$x.'"><img '.$output.' /></a>';	

	return $output;

}


