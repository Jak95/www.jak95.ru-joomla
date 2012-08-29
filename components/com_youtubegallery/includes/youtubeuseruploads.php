<?php 




class VideoSource_YoutubeUserUploads
{
	function extractYouTubeUserID($youtubeURL)
	{
		//link example: http://www.youtube.com/user/designcompasscorp
		$matches=explode('/',$youtubeURL);
	
		if (count($matches) >3)
		{
			
			$userid = $matches[4];
			
			return $userid;
		}
				
	    return '';
	}
	
	function getVideoIDList($youtubeURL,$optionalparameters,&$userid)
	{
		$optionalparameters_arr=explode(',',$optionalparameters);
		$videolist=array();
		
		$spq=implode('&',$optionalparameters_arr);
		
		$userid=VideoSource_YoutubeUserUploads::extractYouTubeUserID($youtubeURL);
		
		if($userid=='')
			return $videolist; //user id not found
		
		$url = 'http://gdata.youtube.com/feeds/api/users/'.$userid.'/uploads?v=2'.($spq!='' ? '&'.$spq : '' ) ; //&max-results=10

		if (ini_get('allow_url_fopen') == true)
		{
			$xml = simplexml_load_file($url);
		}
		elseif ( function_exists('curl_init'))
		{
			$c = curl_init();
			$t = 3; 
			curl_setopt ($c, CURLOPT_URL, $url);
			curl_setopt ($c, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($c, CURLOPT_CONNECTTIMEOUT, $t);
			$xml = simplexml_load_string(curl_exec($c));
			curl_close($c);		
		}
		else return 'Cannot load data, enable "allow_url_fopen"';
		
		if($xml)
		{
			foreach ($xml->entry as $entry)
			{
				
				$attr=$entry->link[0]->attributes();

				if(isset($entry->link[0]) && $attr['rel'] == 'alternate')
				{
					$videolist[] = $attr['href'];
                    
				} else {
					$attr=$entry->link[1]->attributes();
					$videolist[] = $attr['href'];
                    		}

			}
			
		}
		
		return $videolist;
		
	}
	

}


?>