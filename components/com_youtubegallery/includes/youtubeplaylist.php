<?php 




class VideoSource_YoutubePlaylist
{
	function extractYouTubePlayListID($youtubeURL)
	{
				
		$arr=$this->parse_query($youtubeURL);
		
		$p=$arr['list'];
		
		if(strlen($p)<3)
			return '';
		
		if(substr($p,0,2)!='PL')
			return ''; //incorrect playlist ID
		
	    return substr($p,2); //return without leading "PL"
	}
	
	function getVideoIDList($youtubeURL,$optionalparameters,&$playlistid)
	{
		$optionalparameters_arr=explode(',',$optionalparameters);
		$videolist=array();
		
		$spq=implode('&',$optionalparameters_arr);
		
		$videolist=array();
		
		$playlistid=VideoSource_YoutubePlaylist::extractYouTubePlayListID($youtubeURL);
		if($playlistid=='')
			return $videolist; //playlist id not found
		
		$url = 'http://gdata.youtube.com/feeds/api/playlists/'.$playlistid.($spq!='' ? '?'.$spq : '' ) ; //&max-results=10;
		
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