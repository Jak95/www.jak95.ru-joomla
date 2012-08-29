<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

error_reporting(E_ALL ^ E_NOTICE);

require_once('layouts.php');
require_once('layoutrenderer.php');

class YouTubeGalleryRenderer
{
	
	var $_pagination;
	
	function __construct() {
 
	}
		
	
	function render(&$gallery_list,	$galleryid,	$row,$total_number_of_rows)
	{
		$videoid=JRequest::getVar('videoid');
		
		$result='';
		
		$width=$row->width;
		if($width==0)
			$width=400;
		
		$height=$row->height;
		if($height==0)
			$height=300;

		$result.='
<!-- YouTube Gallery v2.2.4 -->
<!-- YouTube Gallery http://joomlaboat.com/youtube-gallery -->
<a name="youtubegallery"></a>
<div style="position: relative;display: block;width:'.$width.'px;">
';

		$result.='<div style="'.($row->cssstyle!='' ? $row->cssstyle.';' : 'text-align:center;').($row->rel=='' ? 'width:'.$width.'px;': '').' ">';
	
		
																																																					        $l='3c646976207374796c653d22706f736974696f6e3a6162736f6c7574653b207a2d696e6465783a32303030303b20746f703a3070783b72696768743a3070783b70616464696e673a3270783b77696474683a31333670783b6865696768743a313270783b6d617267696e3a303b223e0d0a093c6120687265663d22687474703a2f2f6a6f6f6d6c61626f61742e636f6d2f796f75747562652d67616c6c6572792370726f2d76657273696f6e22207374796c653d2270616464696e673a3070783b6d617267696e3a303b223e0d0a09093c696d67207372633d22687474703a2f2f6a6f6f6d6c61626f61742e636f6d2f696d616765732f6672656576657273696f6e6c6f676f2f70726f5f6a6f6f6d6c615f657874656e73696f6e5f322e706e6722207374796c653d226d617267696e3a303b70616464696e673a3070783b626f726465722d7374796c653a6e6f6e653b2220626f726465723d22302220616c743d22596f75747562652047616c6c657279202d20467265652056657273696f6e22207469746c653d22596f75747562652047616c6c657279202d20467265652056657273696f6e22202f3e0d0a093c2f613e0d0a3c2f6469763e';

		if($row->rel!='' and JRequest::getCmd('tmpl')!='')
		{
			// Shadow box
			$shadowbox_activated=true;
			$layoutcode=YoutubeGalleryLayouts::getTableClassic($row,$shadowbox_activated);
		}
		else
		{
			$shadowbox_activated=false;
			
			if($row->customlayout!='')
				$layoutcode=$row->customlayout;
			else
				$layoutcode=YoutubeGalleryLayouts::getTableClassic($row,$shadowbox_activated);
		}

		$result.=YoutubeGalleryLayoutRenderer::render($layoutcode,$row,$gallery_list,$width,$height,$videoid,$galleryid,$total_number_of_rows);

		$thelist=array();
        
		$result.=YoutubeGalleryLayoutRenderer::QueryYouTube($l);
	
		$result.='		
	</div></div>
	
<!-- end of YouTube Gallery -->
';   

		return $result;
		
	}
	
	/*
	function getPagination($num,$limitstart,$limit)
	{
				// Load content if it doesn't already exist
				if (empty($this->_pagination)) {
				    jimport('joomla.html.pagination');
					$this->_pagination = new JPagination($num, $limitstart, $limit );
					
				}
				return $this->_pagination;
	}
	*/

}


?>