<?PHP
/*------------------------------------------------------------------------
# windows.php for PLG - SYSTEM - IMAGESIZER
# ------------------------------------------------------------------------
# author    reDim - Norbert Bayer
# copyright (C) 2011 redim.de. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.redim.de
# Technical Support:  Forum - http://www.redim.de/kontakt/
-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted access' );


function ImageSizer_addon_GetImageHTML(&$ar,&$img,&$imagesizer){


	#JHTML::_( 'behavior.modal' ); 

	$output=plgSystemimagesizer::make_img_output($ar);

	if(isset($ar["title"])){
		$title=$ar["title"];
	}else{
		$title="";
	} 
	
	$img->get_image_size();
	
	$width=$img->width;
	$height=$img->height;
	
	$output='<a class="'.$imagesizer->params->get("linkclass","linkthumb").'" onclick="window.open(this.href,\''.$title.'\',\'status=no,toolbar=no,scrollbars=no,titlebar=no,menubar=no,resizable=yes,width='.$width.',height='.$height.',directories=no,location=no\'); return false;" target="_blank" title="'.$title.'" href="'.$ar["href"].'"><img '.$output.' /></a>';	
	
	return $output;

}


