<?php

class VideoSource_Vimeo
{

	function extractVimeoID($theLink)
	{
		
		preg_match('/http:\/\/vimeo.com\/(\d+)$/', $theLink, $matches);
		if (count($matches) != 0)
		{
			$vimeo_id = $matches[1];
			
			return $vimeo_id;
		}
		
		return '';
	}

	
	function getVideoData($videoid,$customimage,$customtitle,$customdescription, $video_showtitle_nav_or_active,$video_showdescription)
	{
		$theTitle='';
		$Description='';
		$theImage='';

						
		if($customimage=='' or $customtitle=='' or $customdescription=='')
		{
			if(ini_get('allow_url_fopen'))
			{
				$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videoid.".php"));
							
								
				$theImage=$hash[0]['thumbnail_medium'];
			}
		}
						
						
		if($customimage!='')
			$theImage=$customimage;
						
						
		if($video_showtitle_nav_or_active)
		{
			if($customtitle=='')
			{
				if(ini_get('allow_url_fopen'))
					$theTitle=$hash[0]['title'];
			}
			else
				$theTitle=$customtitle;
						
		}//if($video_showtitle_nav_or_active)
						
		if($video_showdescription)
		{
			if($customdescription=='')
			{
				if(ini_get('allow_url_fopen'))
					$Description=$hash[0]['description'];
			}
			else
				$Description=$customdescription;

		}//if($video_showdescription)
						
							
		return array('videosource'=>'vimeo', 'videoid'=>$videoid, 'imageurl'=>$theImage, 'title'=>$theTitle,'description'=>$Description);

		
	}
	
	function renderVimeoPlayer($options)
	{
		$result='<iframe src="http://player.vimeo.com/video/'.$options['videoid'].'?';
		
		if($options['color1']!='')
			$result.='color='.$options['color1'].'&amp;';
			
		if($options['showinfo']==0)
			$result.='portrait=0&amp;title=0&amp;byline=0&amp;';
		
		
		$result.='autoplay='.(int)$options['autoplay'].'&amp;'
			.'loop='.(int)$options['repeat'].'"'
			.'width="'.$options['width'].'" height="'.$options['height'].'" frameborder="'.(int)$options['border'].'"></iframe>';
		
		return $result;
	}

}


?>
