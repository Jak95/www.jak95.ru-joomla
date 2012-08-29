<?php

//not finished
class VideoSource_Break
{


	function extractBreakID($theLink,&$HTML_SOURCE)
	{
		//<meta name="embed_video_url" content="http://embed.break.com/123456">
		
		if(ini_get('allow_url_fopen'))
			$HTML_SOURCE=file_get_contents($theLink);
		else
			return ''; //cannot load file to get VideoID
		
		$ActualLink=VideoSource_Break::getValueByAlmostTag($HTML_SOURCE,'<meta name="embed_video_url" content="');
		
		preg_match('/break.com\/(\d+)$/', $ActualLink, $matches);
		if (count($matches) != 0)
		{
			$video_id = $matches[1];

			return $video_id;
		}

		return '';
	}
	
	function getValueByAlmostTag($HTML_SOURCE,$AlmostTagStart,$AlmostTagEnd='"')
	{
		$vlu='';
		
		$strPartLength=strlen($AlmostTagStart);
		$p1=strpos($HTML_SOURCE,$AlmostTagStart);
		if($p1>0)
		{
			$p2=strpos($HTML_SOURCE,$AlmostTagEnd,$p1+$strPartLength);
			$vlu=substr($HTML_SOURCE,$p1+$strPartLength,$p2-$p1-$strPartLength);
		}
		return $vlu;
	}
	
	function getVideoData($videoid,$customimage,$customtitle,$customdescription, $video_showtitle_nav_or_active,$video_showdescription,&$HTML_SOURCE='')
	{
		$theTitle='';
		$Description='';
		$theImage='';
		
					
		if($customimage!='')
			$theImage=$customimage;
		else
			$theImage=VideoSource_Break::getValueByAlmostTag($HTML_SOURCE,'<meta name="embed_video_thumb_url" content="');		
		
						
						
		if($video_showtitle_nav_or_active)
		{
			if($customtitle=='')
			{
					$theTitle=VideoSource_Break::getValueByAlmostTag($HTML_SOURCE,'<meta name="embed_video_title" id="vid_title" content="');		
			}
			else
				$theTitle=$customtitle;
						
		}//if($video_showtitle_nav_or_active)
						
		if($video_showdescription)
		{
			if($customdescription=='')
			{
					$Description=VideoSource_Break::getValueByAlmostTag($HTML_SOURCE,'<meta name="embed_video_description" id="vid_desc" content="');		
			}
			else
				$Description=$customdescription;

		}//if($video_showdescription)
						
							
		return array('videosource'=>'break', 'videoid'=>$videoid, 'imageurl'=>$theImage, 'title'=>$theTitle,'description'=>$Description);

		
	}


	function renderBreakPlayer($options)
	{
		$result='';
		
		$result.=
		
		'<object '
			.'width="'.$options['width'].'" '
			.'height="'.$options['height'].'" '
			.'id="'.$options['videoid'].'" '
			.'type="application/x-shockwave-flash" '
			.'classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" '
			.'alt="'.$options['title'].'"> ';
		
		$result.=''
			.'<param name="movie" value="http://embed.break.com/'.base64_encode($options['videoid']).'" />'
			.'<param name="allowScriptAccess" value="always" />'
			.'<param name="flashvars" value="playerversion=12&defaultHD=true" />'
			
			.'<param name="allowFullScreen" value="'.($options['fullscreen'] ? 'true' : 'false').'" />';
			
		
			
			
		$result.=''
			.'<embed src="http://embed.break.com/'.base64_encode($options['videoid']).'" '
				.'type="application/x-shockwave-flash" '
				.'flashvars="playerversion=12&defaultHD=true" '
				.'allowScriptAccess="always" '
				
				.'allowfullscreen="'.($options['fullscreen'] ? 'true' : 'false').'" '
				.'width="'.$options['width'].'" '
				.'height="'.$options['height'].'" />'
		.'</object>';
		
	
		return $result;
	}
}


?>