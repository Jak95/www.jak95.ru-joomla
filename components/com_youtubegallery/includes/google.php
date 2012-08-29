<?php

class VideoSource_Google
{
		
	function extractGoogleID($theLink)
	{
				
		$arr=$this->parse_query($theLink);
	    return $arr['docid'];
	}
	
	function getVideoData($videoid,$customimage,$customtitle,$customdescription, $video_showtitle_nav_or_active,$video_showdescription)
	{
		$theTitle='';
		$Description='';
		$theImage='';
						
		//if($firstvideo=='')
			//$firstvideo=$videoid;
		$XML_SOURCE='';
							
		if($customimage!='')
			$theImage=$customimage;
		else
		{
			if(ini_get('allow_url_fopen'))
			{
				$XML_SOURCE=file_get_contents('http://video.google.com/videofeed?docid='.$videoid);
				$match = array();
				preg_match("/media:thumbnail url=\"([^\"]\S*)\"/siU",$XML_SOURCE,$match);
				$theImage=$match[1];
			}//if(ini_get('allow_url_fopen'))
		}//if($customimage!='')
						
						
						
		if($video_showtitle_nav_or_active or $video_showdescription)
		{
							
			$theTitle='Google Video';
						
			if($customtitle!='')
				$theTitle=$customtitle;
									
			if($customdescription!='')
				$Description=$customdescription;
									
			if(!$video_showtitle_nav_or_active)
				$theTitle='';
								
			if(!$video_showdescription)
				$Description='';
							
			return array('videosource'=>'google', 'videoid'=>$videoid, 'imageurl'=>$theImage, 'title'=>$theTitle,'description'=>$Description);
		}
		else
			return array('videosource'=>'google', 'videoid'=>$videoid, 'imageurl'=>$theImage, 'title'=>'','description'=>'');
		
	}
	
	
	function renderGooglePlayer($options)
	{
		//docid=-1667589095394987118
		
		$result='<embed id=VideoPlayback src='
			.'http://video.google.com/googleplayer.swf?'
			.'docid='.$options['videoid'].'&'
			.'hl=en&'
			.'fs='.($options['fullscreen'] ? 'true' : 'false').' '
			.'style=width:'.$options['width'].'px;'
			.'height:'.$options['height'].'px; '
			.'allowFullScreen='.($options['fullscreen'] ? 'true' : 'false').' '
			.'allowScriptAccess="always" '
			.($options['autoplay'] ? 'Flashvars="autoPlay=true" ' : '')
			
			.'type=application/x-shockwave-flash>'
		.'</embed>';
		
		return $result;
	}
	
}