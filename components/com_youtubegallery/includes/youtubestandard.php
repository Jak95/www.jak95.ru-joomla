<?php 




class VideoSource_YoutubeStandard
{

	
	function getVideoIDList($youtubeURL,$optionalparameters,&$playlistid)
	{
		$linkPair=explode(':',$youtubeURL);
		
		if(!isset($linkPair[1]))
			return array();	
		
		$url='';
		
		$playlistid=$linkPair[1];
		
		switch($linkPair[1])
		{
			case 'top_rated':
				$url='https://gdata.youtube.com/feeds/api/standardfeeds/top_rated';
				break;
			
			case 'top_favorites':
				$url='https://gdata.youtube.com/feeds/api/standardfeeds/top_favorites';
				break;
			
			case 'most_viewed':
				$url='https://gdata.youtube.com/feeds/api/standardfeeds/most_viewed';
				break;
			
			case 'most_shared':
				$url='https://gdata.youtube.com/feeds/api/standardfeeds/most_shared';
				break;
			
			case 'most_popular':
				$url='https://gdata.youtube.com/feeds/api/standardfeeds/most_popular';
				break;
			
			case 'most_recent':
				$url='https://gdata.youtube.com/feeds/api/standardfeeds/most_recent';
				break;
			
			case 'most_discussed':
				$url='https://gdata.youtube.com/feeds/api/standardfeeds/most_discussed';
				break;
			
			case 'most_responded':
				$url='https://gdata.youtube.com/feeds/api/standardfeeds/most_responded';
				break;
			
			case 'recently_featured':
				$url='https://gdata.youtube.com/feeds/api/standardfeeds/recently_featured';
				break;
			
			case 'on_the_web':
				$url='https://gdata.youtube.com/feeds/api/standardfeeds/on_the_web';
				break;
			
			default:
				return array();	
			break;
		}
			
		
		$optionalparameters_arr=explode(',',$optionalparameters);
		$videolist=array();
		
		$spq=implode('&',$optionalparameters_arr);
		
		
		$url.= ($spq!='' ? '?'.$spq : '' );
		
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
		
		if($xml){
			foreach ($xml->entry as $entry)
			{
				/*
				if(isset($entry->link[0]))
				{
					$link=$entry->link[0];
					$attr = $link->attributes();
					
					$videolist[] = $attr['href'];
				}
				*/
				
				//
				$media = $entry->children('http://search.yahoo.com/mrss/');
				$link = $media->group->player->attributes();
				if(isset($link['url']))
				{
					$videolist[] = $link['url'];
				}
				//
				
			}
		}
		
		return $videolist;
		
	}
	

}


?>