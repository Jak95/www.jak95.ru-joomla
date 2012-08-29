<?php

class VideoSource_Yahoo
{
	
	
	function extractYahooID($theLink)
	{
		//http://animalvideos.yahoo.com/?vid=25433859&lid=24721185
		
		$l=explode('/',$theLink);
		if(count($l)>5)
			return $l[4].'*'.$l[5];
		
		
		return '';
	}
	
	function getVideoData($videoid,$customimage,$customtitle,$customdescription, $video_showtitle_nav_or_active,$video_showdescription,$theLink)
	{
		echo '<!-- ';
		$theTitle='';
		$Description='';
		$theImage='';
						
						
		if(($customimage=='' or $customtitle=='' or $customdescription=='')
			and ini_get('allow_url_fopen')
			and ($video_showtitle_nav_or_active or $video_showdescription)
			)
			$XML_SOURCE=file_get_contents('http://video.yahoo.com/services/oembed?url='.$theLink);
		else $XML_SOURCE='';
						
		if($customimage=='')
		{
			
			if(ini_get('allow_url_fopen'))
			{
				$p1=strpos($XML_SOURCE,'thumbUrl=');
				if($p1>0)
				{
					$p2=strpos($XML_SOURCE,'.jpg',$p1);
					$theImage=substr($XML_SOURCE,$p1+9,$p2-$p1-9+4);
					$theImage=str_replace('\\','',$theImage);
				}
			}
			if($theImage=='')
				$theImage='component/com_youtubegallery/images/yahoo.jpg';
		}
		else
			$theImage=$customimage;
						
		if($video_showtitle_nav_or_active)
		{
			if($customtitle=='')
			{
			if(ini_get('allow_url_fopen'))
			{
				$theTitle='Yahoo Video';
				$p1=strpos($XML_SOURCE,'"title":"');
				if($p1>0)
				{
					$p2=strpos($XML_SOURCE,'"',$p1+9);
					$theTitle=substr($XML_SOURCE,$p1+9,$p2-$p1-9);
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
					$Description='Yahoo Video';
					$p1=strpos($XML_SOURCE,'"description":"');
					if($p1>0)
					{
						$p2=strpos($XML_SOURCE,'"',$p1+9);
						$Description=substr($XML_SOURCE,$p1+9,$p2-$p1-9);
					}
				}//if(ini_get('allow_url_fopen'))
			}
			else
				$Description=$customdescription;
						
		}
		
		echo ' -->';
													
		return array(
												  'videosource'=>'yahoo',
												  'videoid'=>$videoid,
												  'imageurl'=>$theImage,
												  'title'=>$theTitle,
												  'description'=>$Description
												  
												  );	
	}
	
	
	function renderYahooPlayer($options)
	{
		return '<p>Not sopported in this version due to Yahoo service changes.</p>';
	
		$idpair=explode('*',$options['videoid']);
		
		$image=str_replace(':','%3A',$options['thumbnail']);
		
		$result='<object width="'.$options['width'].'" height="'.$options['height'].'">'
			.'<param name="movie" value="http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.46" />'
			.'<param name="allowFullScreen" value="'.($options['fullscreen'] ? 'true' : 'false').'" />'
			.'<param name="AllowScriptAccess" VALUE="always" />'
			.'<param name="bgcolor" value="'.($options['color1']!='' ? '#'.$options['color1'] : '#000000' ).'" />'
			.'<param name="flashVars" '
				.'value="id='.$idpair[1].'&'
				.'vid='.$idpair[0].'&lang=en-us&'
				.'intl=us&'
				.'thumbUrl='.$image.'&'
				.'embed=1&'
				.($options['autoplay'] ? 'autoPlay=true&' : '')
				.'ap=20683543'
				.'"'
			.'/>'
			
			.'<embed src="http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.46" '
			.'type="application/x-shockwave-flash" '
			.'width="'.$options['width'].'" '
			.'height="'.$options['height'].'" '
			.'allowFullScreen="'.($options['fullscreen'] ? 'true' : 'false').'" '
			.'AllowScriptAccess="always" '
			.'bgcolor="'.($options['color1']!='' ? '#'.$options['color1'] : '#000000' ).'" '
			.'flashVars='
			.'"id='.$idpair[1].'&'
				.'vid='.$idpair[0].'&'
				.'lang=en-us&'
				.'intl=us&'
				.'thumbUrl='.$image.'&'
				.'embed=1&'
				.($options['autoplay'] ? 'autoPlay=true&' : '')
				.'ap=20683543"'
			.'>'
			.'</embed></object>';
			

		return $result;
	}	
}