<?php 




class VideoSource_YouTube
{
	function extractYouTubeID($youtubeURL)
	{
		if(!(strpos($youtubeURL,'youtu.be')===false))
		{
			//youtu.be
			$list=explode('/',$youtubeURL);
			if(isset($list[3]))
				return $list[3];
			else
				return '';
		}
		else
		{
			//youtube.com
			$arr=$this->parse_query($youtubeURL);
			return $arr['v'];	
		}
		
	}
	
	function getVideoData($videoid,$customimage,$customtitle,$customdescription, $video_showtitle_nav_or_active,$video_showdescription,$thumbnailcssstyle)
	{
			
		$theTitle='';
		$Description='';
		$theImage='';
						
							
		if($customimage!='')
			$theImage=$customimage;
		else
			$theImage=VideoSource_YouTube::getYouTubeImageURL($videoid,$thumbnailcssstyle);
			
		
							
		if($video_showtitle_nav_or_active or $video_showdescription)
		{
							
							
			if($customtitle!='' and $customdescription!='')
			{
				$theTitle=$customtitle;
				$Description=$customdescription;
			}
			else
			{
				$theTitle=VideoSource_YouTube::getYouTubeVideoTitleAndDescription($videoid,$Description);
								
				if($customtitle!='')
					$theTitle=$customtitle;

				if($customdescription!='')
					$Description=$customdescription;
			}

			/*
			if(!$video_showtitle_nav_or_active)
				$theTitle='';
								
			if(!$video_showdescription)
				$Description='';
				
			*/
							
			return array(
				'videosource'=>'youtube',
				'videoid'=>$videoid,
				'imageurl'=>$theImage,
				'title'=>$theTitle,
				'description'=>$Description
				);
		}
		else
			return array('videosource'=>'youtube', 'videoid'=>$videoid, 'imageurl'=>$theImage,'title'=>'','description'=>'');
							
									
		
	}
	
	function getYouTubeImageURL($videoid,$thumbnailcssstyle)
	{
		
		
		if($thumbnailcssstyle == null)
			return 'http://img.youtube.com/vi/'.$videoid.'/default.jpg';
		
		//get bigger image if size of the thumbnail set;
		
		$a=str_replace(' ','',$thumbnailcssstyle);
		if(strpos($a,'width:')===false and strpos($a,'height:')===false)
			return 'http://img.youtube.com/vi/'.$videoid.'/default.jpg';
		else
			return 'http://img.youtube.com/vi/'.$videoid.'/0.jpg';
		
	}
	
	function getYouTubeVideoTitleAndDescription($videoid,&$description)
	{
		if(phpversion()<5)
			return "Update to PHP 5+";
				
		if(!ini_get('allow_url_fopen'))
			return 'Set "allow_url_fopen=on" in PHP.ini file.';
				



			
			//$doc = new DOMDocument;
			//$doc->load($url);
			//$tplusd =$doc->getElementsByTagName("name")->item(0)->nodeValue;
			
	

		try{
		echo '<!-- ';
		$value=eval('
			$url = "http://gdata.youtube.com/feeds/api/videos/'.$videoid.'";			
			$doc = new DOMDocument;
			$doc->load($url);
			$tplusd =$doc->getElementsByTagName("title")->item(0)->nodeValue;
			$tplusd.="<!--and-->";
			$tplusd.=$doc->getElementsByTagName("description")->item(0)->nodeValue;
			return $tplusd;');
		echo ' --> ';
		
		
		}
		catch(Exception $e)
		{
			$description='cannot get youtibe video data';
			return 'cannot get youtibe video data';
		}
		
		$pair=explode('<!--and-->',$value);
		$description=$pair[1];
		return $pair[0];
	}
	
	function renderYouTubePlayer($options,&$row)
	{
		
		$settings=array();
		
		$settings[]=array('autoplay',(int)$options['autoplay']);
		
		$settings[]=array('hl','en');
		$settings[]=array('fs','1');
		$settings[]=array('showinfo',$options['showinfo']);
		$settings[]=array('iv_load_policy','3');
		$settings[]=array('rel',$options['relatedvideos']);
		$settings[]=array('loop',(int)$options['repeat']);
		$settings[]=array('border',(int)$options['border']);
		
		if($options['color1']!='')
			$settings[]=array('color1',$options['color1']);
			
		if($options['color2']!='')
			$settings[]=array('color2',$options['color2']);

		if($options['controls']!='')
		{
			$settings[]=array('controls',$options['controls']);
			if($options['controls']==0)
				$settings[]=array('version',3);
			
		}
		
		if($row->muteonplay)
			$options['playertype']=2; //becouse other types of player doesn't support this functionality.
		
		$playerapiid='ygplayerapiid_'.$row->id;
		$playerid='youtubegalleryplayerid_'.$row->id;
		
		if($options['playertype']==2)
		{
			//Player with Flash availability check
			$settings[]=array('playerapiid','ygplayerapiid_'.$playerapiid);
			$settings[]=array('enablejsapi','1');
		}
		
		VideoSource_YouTube::ApplyYoutubeParameters($settings,$options['youtubeparams']);
		
		$settingline=VideoSource_YouTube::CreateParamLine($settings);
		
		$result='';
		
		if($options['playertype']==1) //new HTML 5 player
		{
			//new player
			$result.='
			<iframe width="'.$options['width'].'" height="'.$options['height'].'" '
				.'src="http://www.youtube.com/embed/'.$options['videoid'].'?'.$settingline.'" '
				.'frameborder="'.(int)$options['border'].'" >'
			.'</iframe>';
			//.'allowfullscreen="always" NOT W3C compatible
		}
		elseif($options['playertype']==0) //Flash Player
		{
			$p=explode(';',$options['youtubeparams']);
			$playlist='';
			foreach($p as $v)
			{
				$pair=explode('=',$v);
				if($pair[0]=='playlist')
				{
					$playlist=$pair[1];
				}
			}
			
			//Old player
			$result.='
			<object width="'.$options['width'].'" height="'.$options['height'].'" data="http://www.youtube.com/v/'.$options['videoid'].'?version=3&amp;'.$settingline.'" type="application/x-shockwave-flash">'
				.'<param name="movie" value="http://www.youtube.com/v/'.$options['videoid'].'?version=3&amp;'.$settingline.'" />'
				.'<param name="wmode" value="transparent" />'
				.'<param name="allowFullScreen" value="'.($options['fullscreen'] ? 'true' : 'false').'" />'
				.'<param name="allowscriptaccess" value="always" />'
				.($playlist!='' ? '<param name="playlist" value="'.$playlist.'" />' : '');
			$result.='</object>';
		}
		elseif($options['playertype']==2) //Flash Player with detection
		{
			$initial_volume=(int)$row->volume;
			if($initial_volume>100)
				$initial_volume=100;
			if($initial_volume<-1)
				$initial_volume=-1;
			
			$p=explode(';',$options['youtubeparams']);
			$playlist='';
			foreach($p as $v)
			{
				$pair=explode('=',$v);
				if($pair[0]=='playlist')
					$playlist=$pair[1];
			}
			
			//Old player
			$result_head='
			<!-- Youtube Gallery - Youtube Flash Player With Detection -->
			<script src="http://www.google.com/jsapi" type="text/javascript"></script>
			<script src="http://ajax.googleapis.com/ajax/libs/swfobject/2/swfobject.js" type="text/javascript"></script>
			<script type="text/javascript">
			//<![CDATA[
			    google.load("swfobject", "2");
			    function onYouTubePlayerReady(playerId) {
			        ytplayer = document.getElementById("'.$playerid.'");
					'.($row->muteonplay ? 'ytplayer.mute();' : '').'
					'.($initial_volume!=-1 ? 'setTimeout("changeVolumeAndPlay(\'"+playerId+"\')", 750);' : '').'
			    }
				'.($initial_volume!=-1 ? '
				function changeVolumeAndPlay(playerId) {
					ytplayer = document.getElementById("'.$playerid.'");
					if(ytplayer) {
					
						ytplayer.setVolume('.$initial_volume.');
				        '.($row->autoplay ? 'ytplayer.playVideo();' : '').'
					}
				}   
				' : '').'
			//]]>
			</script>
			<!-- end of Youtube Gallery - Youtube Flash Player With Detection -->
			';
			
			$result.='
			<div id="'.$playerapiid.'">You need Flash player 8+ and JavaScript enabled to view this video.</div>
			<script type="text/javascript">
			//<![CDATA[
			    var params = { allowScriptAccess: "always", wmode: "transparent" };
			    var atts = { id: "'.$playerid.'" };
			    swfobject.embedSWF("http://www.youtube.com/v/'.$options['videoid'].'?version=3&amp;'.$settingline.'","'.$playerapiid.'", "'.$options['width'].'", "'.$options['height'].'", "8", null, null, params, atts);
			//]]>
			</script>';
			
			$document =& JFactory::getDocument();
			$document->addCustomTag($result_head);
			
		}

		return $result;
	}
	
	function ApplyYoutubeParameters(&$settings,$youtubeparams)
	{
		if($youtubeparams=='')
			return;
		
		$a=str_replace("\n",'',$youtubeparams);
		$a=trim(str_replace("\r",'',$a));
		$l=explode(';',$a);
		
		foreach($l as $o)
		{
			if($o!='')
			{
				$pair=explode('=',$o);
				if(count($pair)==2)
				{
					$option=trim(strtolower($pair[0]));
			
					$found=false;
			
					for($i=0;$i<count($settings);$i++)
					{
				
						if($settings[$i][0]==$option)
						{
							$settings[$i][1]=$pair[1];
							$found=true;
							break;
						}
					}
				
					if(!$found)
						$settings[]=array($option,$pair[1]);
				}//if(count($pair)==2)
			}//if($o!='')
		}
		
	}
	
	function CreateParamLine(&$settings)
	{
		$a=array();
		
		foreach($settings as $s)
			$a[]=$s[0].'='.$s[1];

		return implode('&amp;',$a);
	}

}


?>