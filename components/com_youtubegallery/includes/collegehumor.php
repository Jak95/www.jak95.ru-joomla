<?php

//not finished
class VideoSource_CollegeHumor
{


	function extractCollegeHumorID($theLink)
	{
		$l=explode('/',$theLink);
		if(count($l)>5)
			return $l[4];
		
		
		return '';
		
	}
	
	function getVideoData($videoid,$customimage,$customtitle,$customdescription, $video_showtitle_nav_or_active,$video_showdescription)
	{
		$theTitle='';
		$Description='';
		$theImage='';
						
						
		if(($customimage=='' or $customtitle=='' or $customdescription=='')
			and ini_get('allow_url_fopen')
			and ($video_showtitle_nav_or_active or $video_showdescription)
			)
			$HTML_SOURCE=file_get_contents('http://www.collegehumor.com/video/'.$videoid);
		else $HTML_SOURCE='';
					
		if($customimage=='')
		{
			if(ini_get('allow_url_fopen'))
			{
				$strPart='<meta name="og:image" content="';
				$strPartLength=strlen($strPart);
				
				$p1=strpos($HTML_SOURCE,$strPart);
				if($p1>0)
				{
					$p2=strpos($HTML_SOURCE,'"',$p1+$strPartLength);
					$theImage=substr($HTML_SOURCE,$p1+$strPartLength,$p2-$p1-$strPartLength);
					$theImage=str_replace('\\','',$theImage);
				}
			}
			if($theImage=='')
				$theImage='';
		}
		else
			$theImage=$customimage;
						
		if($video_showtitle_nav_or_active)
		{
			if($customtitle=='')
			{
			if(ini_get('allow_url_fopen'))
			{
				$theTitle='CollegeHumor';
				$strPart='<meta name="og:title" content="';
				$strPartLength=strlen($strPart);
				$p1=strpos($HTML_SOURCE,$strPart);
				if($p1>0)
				{
					$p2=strpos($HTML_SOURCE,'"',$p1+$strPartLength);
					$theTitle=substr($HTML_SOURCE,$p1+$strPartLength,$p2-$p1-$strPartLength);
					}
				}//if(ini_get('allow_url_fopen'))
			}
			else
				$theTitle=$customtitle;
						
		}
						
		if($video_showdescription)
		{
			if($customdescription=='')
			{
				if(ini_get('allow_url_fopen'))
				{
					$Description='CollegeHumor';
					$strPart='<meta name="description" content="';
					$strPartLength=strlen($strPart);
					$p1=strpos($HTML_SOURCE,$strPart);
					if($p1>0)
					{
						$p2=strpos($HTML_SOURCE,'"',$p1+$strPartLength);
						$Description=substr($HTML_SOURCE,$p1+$strPartLength,$p2-$p1-$strPartLength);
					}
				}//if(ini_get('allow_url_fopen'))
			}
			else
				$Description=$customdescription;
						
		}
		
													
		return array(
												  'videosource'=>'collegehumor',
												  'videoid'=>$videoid,
												  'imageurl'=>$theImage,
												  'title'=>$theTitle,
												  'description'=>$Description
												  
												  );	
	}


	

	function renderCollegeHumorPlayer($options)
	{
		$result='';
		
		$result.=
		
		'<object '
			.'id="" '
			.'type="application/x-shockwave-flash" '
			.'data="http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id='.$options['videoid'].'&use_node_id=true&fullscreen='.($options['fullscreen'] ? '1' : '0').'" '
			.'width="'.$options['width'].'" '
			.'height="'.$options['height'].'" '
			.'alt="'.$options['title'].'"> ';
		
		$result.=''
			.'<param name="movie" quality="best" value="http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id='.$options['videoid'].'&use_node_id=true&fullscreen='.($options['fullscreen'] ? '1' : '0').'" />'
			.'<param name="allowScriptAccess" value="always" />'
			.'<param name="allowFullScreen" value="'.($options['fullscreen'] ? 'true' : 'false').'" />'
			.'<param name="wmode" value="transparent"/>';
			
		//first 8 chars is a video id
		$result.=''
			.'<embed src="http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id='.$options['videoid'].'&use_node_id=true&fullscreen='.($options['fullscreen'] ? '1' : '0').'" '
				.'type="application/x-shockwave-flash" '
				.'wmode="transparent" '
				.'allowScriptAccess="always" '
				.'allowfullscreen="'.($options['fullscreen'] ? 'true' : 'false').'" '
				.'width="'.$options['width'].'" '
				.'height="'.$options['height'].'" /> '
		.'</object>';
	
		return $result;
	}
}
?>

