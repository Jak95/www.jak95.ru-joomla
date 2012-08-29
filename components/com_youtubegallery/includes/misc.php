<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

error_reporting(E_ALL ^ E_NOTICE);


class YouTubeGalleryMisc
{
	var $tablerow;
	
	function formGalleryList($rawList,$video_showtitle,$video_showdescription,&$firstvideo, $showactivevideotitle,$query_video_host=false)
	{
		$gallery_list=array();
		
		$main_ordering=10000; //10000 step
		
		foreach($rawList as $b)
		{
			
			$b=str_replace("\n",'',$b);
			$b=trim(str_replace("\r",'',$b));
			
			$listitem=$this->csv_explode(',', $b, '"', false);
			
			$theLink=trim($listitem[0]);
			$vsn=$this->getVideoSourceName($theLink);
				
			if(isset($listitem[4]))
				$specialparams=$listitem[4];
			else
				$specialparams='';
				
			if($vsn=='youtubeplaylist')
			{
				require_once('youtubeplaylist.php');
				$newlist=VideoSource_YoutubePlaylist::getVideoIDList($theLink,$specialparams,$playlistid);
			}
			elseif($vsn=='youtubeuserfavorites')
			{
				require_once('youtubeuserfavorites.php');
				$newlist=VideoSource_YoutubeUserFavorites::getVideoIDList($theLink,$specialparams,$playlistid);
			}
			elseif($vsn=='youtubeuseruploads')
			{
				require_once('youtubeuseruploads.php');
				$newlist=VideoSource_YoutubeUserUploads::getVideoIDList($theLink,$specialparams,$playlistid);
			}
			elseif($vsn=='youtubestandard')
			{
				require_once('youtubestandard.php');
				$newlist=VideoSource_YoutubeStandard::getVideoIDList($theLink,$specialparams,$playlistid);
			}	

			
			if($vsn=='youtubeuseruploads' or $vsn=='youtubestandard' or $vsn=='youtubeplaylist' or $vsn=='youtubeuserfavorites') 
			{
				
				$new_List_Clean=array();
			
				$ordering=1;
				foreach($newlist as $theLinkItem)
				{
					$item=$this->GrabVideoData($theLinkItem,'youtube',$video_showtitle,$video_showdescription, $showactivevideotitle,$query_video_host);
					if($item['videoid']!='')
					{
						if($firstvideo=='')
							$firstvideo=$item['videoid'];
						
						$item['ordering']=$main_ordering+$ordering;	
						$new_List_Clean[]=$item;
						
						
						$ordering++;
						
					}	
						
				}
				
				$item=array(
				'videosource'=>$vsn,
				'videoid'=>$playlistid,
				'imageurl'=>$customimage,
				'title'=>$customtitle,
				'description'=>$customdescription,
				'specialparams'=>$specialparams,
				'count'=>count($new_List_Clean),
				'link'=>'',
				'ordering'=>$main_ordering
				
				);
				
				$gallery_list[]=$item;
				$gallery_list=array_merge($gallery_list,$new_List_Clean);
			}
			else
			{
				$item=$this->GrabVideoData($listitem,$vsn,$video_showtitle,$video_showdescription, $showactivevideotitle,$query_video_host);
				if($item['videoid']!='')
				{
					if($firstvideo=='')
							$firstvideo=$item['videoid'];
							
					$item['ordering']=$main_ordering;
					$gallery_list[]=$item;
				}
			}
			
			$main_ordering+=10000;
			
		}//foreach($rawList as $b)
		return $gallery_list;
	}
	
	
	function GrabVideoData($listitem,$vsn,$video_showtitle, $video_showdescription, $showactivevideotitle,$query_video_host,$videoid_optional='')
	{
		
			//$listitem - is custom title, desciption and image
		
			//if($videoid_optional=='')
			$theLink=trim($listitem[0]);
				
			//Return Video Data Array separated with commma
		
			//extract title if it's needed for navigation (thumbnail) or for active video.
			$videoitem=array();
			
			$video_showtitle_nav_or_active=($video_showtitle or $showactivevideotitle ? true : false);
			
		
			if(isset($listitem[1]))
				$customtitle=$listitem[1];
			else
				$customtitle='';
			
			if(isset($listitem[2]))
				$customdescription=$listitem[2];
			else
				$customdescription='';
				
			if(isset($listitem[3]))
				$customimage=$listitem[3];
			else
				$customimage='';
				
		
			switch($vsn)
			{
				
				case 'break' :
					
					require_once('break.php');
					$HTML_SOURCE='';
					
					$videoid=VideoSource_Break::extractBreakID($theLink,$HTML_SOURCE);
					
					if($videoid!='')
					{
						if($query_video_host)
						{
							$videoitem=VideoSource_Break::getVideoData($videoid,$customimage,$customtitle,$customdescription, $video_showtitle_nav_or_active,$video_showdescription,$HTML_SOURCE);
							$videoitem['link']=$theLink;
						}
						else
							$videoitem=array('videosource'=>'break', 'videoid'=>$videoid, 'imageurl'=>$customimage, 'title'=>$customtitle,'description'=>$customdescription,'link'=>$theLink);
					}

					break;
				
				
				case 'vimeo' :
					
					require_once('vimeo.php');
					if($videoid_optional=='')
						$videoid=VideoSource_Vimeo::extractVimeoID($theLink);
					else
						$videoid=$videoid_optional;						
					
					if($videoid!='')
					{
						if($query_video_host)
						{
							$videoitem=VideoSource_Vimeo::getVideoData($videoid,$customimage,$customtitle,$customdescription, $video_showtitle_nav_or_active,$video_showdescription);
							$videoitem['link']=$theLink;
						}
						else
							$videoitem=array('videosource'=>'vimeo', 'videoid'=>$videoid, 'imageurl'=>$customimage, 'title'=>$customtitle,'description'=>$customdescription,'link'=>$theLink);
					}

					break;
				
				case 'youtube' :
					
					require_once('youtube.php');
					if($videoid_optional=='')
						$videoid=VideoSource_Youtube::extractYouTubeID($theLink);
					else
						$videoid=$videoid_optional;
					
					if($videoid!='')
					{
						
						
						if($query_video_host)
						{
						
							$videoitem=VideoSource_Youtube::getVideoData(
												$videoid,
												$customimage,
												$customtitle,
												$customdescription,
												$video_showtitle_nav_or_active,
												$video_showdescription,
												$this->tablerow->thumbnailstyle
												);
							
							$videoitem['link']=$theLink;
						}
						else
							$videoitem=array('videosource'=>'youtube', 'videoid'=>$videoid, 'imageurl'=>$customimage, 'title'=>$customtitle,'description'=>$customdescription,'link'=>$theLink);
						
					}
					break;
				
				case 'google' :

					require_once('google.php');
					if($videoid_optional=='')
						$videoid=VideoSource_Google::extractGoogleID($theLink);
					else
						$videoid=$videoid_optional;

					if($videoid!='')
					{
						if($query_video_host)
						{
							$videoitem=VideoSource_Google::getVideoData($videoid,$customimage,$customtitle,$customdescription, $video_showtitle_nav_or_active,$video_showdescription);
							$videoitem['link']=$theLink;
						}
						else
							$videoitem=array('videosource'=>'google', 'videoid'=>$videoid, 'imageurl'=>$customimage, 'title'=>$customtitle,'description'=>$customdescription,'link'=>$theLink);
					}
					
					break;
				
				case 'yahoo' :
					
					require_once('yahoo.php');
					
					if($videoid_optional=='')
						$videoid=VideoSource_Yahoo::extractYahooID($theLink);
					else
						$videoid=$videoid_optional;
					
					if($videoid!='')
					{
						if($query_video_host)
						{
							$videoitem=VideoSource_Yahoo::getVideoData($videoid,$customimage,$customtitle,$customdescription, $video_showtitle_nav_or_active,$video_showdescription,$theLink);
							$videoitem['link']=$theLink;
						}
						else
							$videoitem=array('videosource'=>'yahoo', 'videoid'=>$videoid, 'imageurl'=>$customimage, 'title'=>$customtitle,'description'=>$customdescription,'link'=>$theLink);
					}
				
					break;
				
				case 'collegehumor' :
					
					require_once('collegehumor.php');
					
					if($videoid_optional=='')
						$videoid=VideoSource_CollegeHumor::extractCollegeHumorID($theLink);
					else
						$videoid=$videoid_optional;
					
					
					if($videoid!='')
					{
						if($query_video_host)
						{
							$videoitem=VideoSource_CollegeHumor::getVideoData($videoid,$customimage,$customtitle,$customdescription, $video_showtitle_nav_or_active,$video_showdescription);
							$videoitem['link']=$theLink;
						}
						else
							$videoitem=array('videosource'=>'collegehumor', 'videoid'=>$videoid, 'imageurl'=>$customimage, 'title'=>$customtitle,'description'=>$customdescription,'link'=>$theLink);
					}
			
					break;
				
				
				
			}//switch($vsn)
			
			$videoitem['custom_title']=$customtitle;
			$videoitem['custom_description']=$customdescription;
			$videoitem['custom_imageurl']=$customimage;
				
		return $videoitem;
	}
	
	
	function isVideo_record_exist($videosource,$videoid,$galleryid)
	{
				$db=& JFactory::getDBO();
				
				$query = 'SELECT id, allowupdates FROM #__youtubegallery_videos WHERE `videosource`="'.$videosource.'" AND `videoid`="'.$videoid.'" AND `galleryid`='.$galleryid.' LIMIT 1';

				$db->setQuery($query);
				if (!$db->query())    die( $db->stderr());
				
				$rows=$db->loadAssocList();
				
				if(count($rows)==0)
						return 0;
				
				$row=$rows[0];
				
				if($row['allowupdates']!=1)
						return -1; //Updates disable
				
				return $row['id'];
	}
	

	function getGalleryList_FromCache_From_Table($galleryid,&$videoid,&$total_number_of_rows)
	{
		
		if(((int)$this->tablerow->customlimit)==0)
			$limit=0; // UNLIMITED
		else
			$limit = (int)$this->tablerow->customlimit;
			
		$limitstart = JRequest::getVar('start', 0, '', 'int');
		
		$db=& JFactory::getDBO();
				
		$query = 'SELECT * FROM #__youtubegallery_videos WHERE `galleryid`="'.$galleryid.'" AND `isvideo` ORDER BY ordering';

		$db->setQuery($query);
		$db->query();
		$total_number_of_rows = $db->getNumRows();
		
		if($limit==0)
			$db->setQuery($query);
		else
			$db->setQuery($query, $limitstart, $limit);
		
		if (!$db->query())    die( $db->stderr());
				


		$rows=$db->loadAssocList();
		
		$firstvideo='';
		
		if($firstvideo=='' and count($rows)>0)
		{
			$row=$rows[0];
			$firstvideo=$row['videoid'];
			
			
		}
		if($videoid!='')
		{
			$found=false;	
			foreach($rows as $row)
			{
								
				if($row['videoid']==$videoid)
					$found=true;
			}
		
			if(!$found)
			{
				$videoid=$firstvideo;
							
			}	
		}
		else
			$videoid=$firstvideo;
	
		
		return $rows;
		
	}
	
	

	function update_playlist(&$row,$force_update = false)
	{
			
			$start  = strtotime( $row->lastplaylistupdate );
			$end    = strtotime( date( 'Y-m-d H:i:s') );
			$days_diff = ($end-$start)/86400;
			
			$updateperiod=$row->updateperiod;
			if($updateperiod==0)
				$updateperiod=1;
			
			if($days_diff>$updateperiod or $force_update)
			{
				$this->update_cache_table($row);
				$row->lastplaylistupdate =date( 'Y-m-d H:i:s');
				
				$db=& JFactory::getDBO();
				$query = 'UPDATE #__youtubegallery SET `lastplaylistupdate`="'.$row->lastplaylistupdate.'" WHERE `id`="'.$row->id.'"';
				$db->setQuery($query);
				if (!$db->query())    die( $db->stderr());
			}
	}

	function update_cache_table(&$row) 
	{
				$gallerylist_array=$this->csv_explode("\n", $row->gallerylist, '"', true);
				
				$firstvideo='';
				$gallerylist=$this->formGalleryList($gallerylist_array,$row->showtitle, $row->description, $firstvideo, $firstvideo->showactivevideotitle);

				$ListOfVideos=array();
				
				$db=& JFactory::getDBO();
				
				$parent_id=0;
				$this_is_a_list=false;
				$list_count_left=0;
				
				foreach($gallerylist as $g)
				{
						$g_title=str_replace('"','&quot;',$g['title']);
						$g_description=str_replace('"','&quot;',$g['description']);
						
						$custom_g_title=str_replace('"','&quot;',$g['custom_title']);
						$custom_g_description=str_replace('"','&quot;',$g['custom_description']);
						
						$fields=array();

						if($g['videosource']=='youtubeuseruploads' or $g['videosource']=='youtubestandard' or $g['videosource']=='youtubeplaylist' or $g['videosource']=='youtubeuserfavorites')
						{
								//parent
								$parent_id=0;
								$this_is_a_list=true;
								$list_count_left=(int)$g['count'];
						}
						else
						{
								$this_is_a_list=false;
						}

						
						$fields[]='`galleryid`="'.$row->id.'"';
						$fields[]='`parentid`="'.$parent_id.'"';
						$fields[]='`videosource`="'.$g['videosource'].'"';
						$fields[]='`videoid`="'.$g['videoid'].'"';
						
						if($g['imageurl']!='')
							$fields[]='`imageurl`="'.$g['imageurl'].'"';
							
						if($g['title']!='')
							$fields[]='`title`="'.$g_title.'"';
						
						if($g['description']!='')
							$fields[]='`description`="'.$g_description.'"';
						
						$fields[]='`custom_imageurl`="'.$g['custom_imageurl'].'"';
						$fields[]='`custom_title`="'.$custom_g_title.'"';
						$fields[]='`custom_description`="'.$custom_g_description.'"';
						
						$fields[]='`specialparams`="'.$g['specialparams'].'"';
						$fields[]='`link`="'.$g['link'].'"';
						$fields[]='`ordering`="'.$g['ordering'].'"';
					
						if($this_is_a_list)
								$fields[]='`lastupdate`="'.date( 'Y-m-d H:i:s').'"';
						$fields[]='`isvideo`="'.($this_is_a_list ? '0' : '1').'"';
						
						$record_id=$this->isVideo_record_exist($g['videosource'],$g['videoid'],$row->id);
						
						$query='';
						

						
						
						if($record_id==0)
						{
								$query="INSERT #__youtubegallery_videos SET ".implode(', ', $fields).', `allowupdates`="1"';
								$db->setQuery($query);
								if (!$db->query())    die( $db->stderr());
								
								$record_id_new=$this->isVideo_record_exist($g['videosource'],$g['videoid'],$row->id);
								
								$ListOfVideos[]=$record_id_new;

								if($this_is_a_list)
										$parent_id=$record_id_new;
						}
						elseif($record_id>0)
						{
								$query="UPDATE #__youtubegallery_videos SET ".implode(', ', $fields).' WHERE id='.$record_id;
								$db->setQuery($query);
								if (!$db->query())    die( $db->stderr());
								
								$ListOfVideos[]=$record_id;
								
								if($this_is_a_list)
										$parent_id=$record_id;
								
						}
								
						if($query!='')
						{
								$db->setQuery($query);
								if (!$db->query())    die( $db->stderr());
						}
						
						
						if(!$this_is_a_list)
						{
								if($list_count_left>0)
										$list_count_left-=1;
										
								
								if($list_count_left==0)
										$parent_id=0;
						}
						

				}
				
				//Delete All videos of this gallery that has bee delete form the list but allowed for updates.
				
				$query='DELETE FROM #__youtubegallery_videos WHERE galleryid='.$row->id.' AND allowupdates';
				if(count($ListOfVideos)>0)
						$query.=' AND id!='.implode(' AND id!=',$ListOfVideos).' ';
				
			
				$db->setQuery($query);
				if (!$db->query())    die( $db->stderr());
				
			
	}
	
	
	function RefreshVideoData(&$gallery_list)
	{
		$db=& JFactory::getDBO();
		
		$count=count($gallery_list);
		for($i=0;$i<$count;$i++)
		{
			$listitem=$gallery_list[$i];
			
			$start  = strtotime( $listitem['lastupdate'] );
			$end    = strtotime( date( 'Y-m-d H:i:s') );
			$days_diff = ($end-$start)/86400;
			
			$updateperiod=$this->tablerow->updateperiod;
			if($updateperiod==0)
				$updateperiod=1;
			
			if($listitem['status']==0 or $days_diff>$updateperiod)
			{
				$listitem_temp=array();
				$listitem_temp[]=$listitem['link'];
				$listitem_temp[]=$listitem['custom_title'];
				$listitem_temp[]=$listitem['custom_description'];
				$listitem_temp[]=$listitem['custom_imageurl'];
				
				$listitem_new=$this->GrabVideoData($listitem_temp,$listitem['videosource'],$this->tablerow->showtitle, $this->tablerow->description, $this->tablerow->showactivevideotitle,true,$listitem['videoid']);
				
				if($listitem_new['title']!='')
					$listitem['title']=$listitem_new['title'];
					
				if($listitem_new['description']!='')
					$listitem['description']=$listitem_new['description'];
					
				if($listitem_new['imageurl']!='')
					$listitem['imageurl']=$listitem_new['imageurl'];
				
				$fields=array();
				
				$fields[]='`title`="'.$this->mysqlrealescapestring($listitem_new['title']).'"';
				$fields[]='`description`="'.$this->mysqlrealescapestring($listitem_new['description']).'"';
				$fields[]='`imageurl`="'.$listitem_new['imageurl'].'"';
				$fields[]='`lastupdate`="'.date( 'Y-m-d H:i:s').'"';
				$fields[]='`status`="200"';
				
				$query="UPDATE #__youtubegallery_videos SET ".implode(', ', $fields).' WHERE id='.$listitem['id'];

				$db->setQuery($query);
				if (!$db->query())    die( $db->stderr());
				
				$gallery_list[$i]=$listitem;
				
			}
			
		}//foreach($gallery_list as $listitem)
		

	}
	
	
	function getVideoSourceName($link)
	{
	
		if(!(strpos($link,'youtube.com')===false))
		{
			if(!(strpos($link,'/playlist')===false))
				return 'youtubeplaylist';
			elseif(!(strpos($link,'/favorites')===false))
				return 'youtubeuserfavorites';
			elseif(!(strpos($link,'/user')===false))
				return 'youtubeuseruploads';
			else
				return 'youtube';
		}
		
		if(!(strpos($link,'youtu.be')===false))
			return 'youtube';
		
		if(!(strpos($link,'youtubestandard:')===false))
			return 'youtubestandard';
		
		if(!(strpos($link,'vimeo.com')===false) or !(strpos($link,'www.vimeo.com')===false))
			return 'vimeo';
		
		if(!(strpos($link,'video.google.com')===false))
			return 'google';
		
		if(!(strpos($link,'video.yahoo.com')===false))
			return 'yahoo';
		
		if(!(strpos($link,'break.com')===false))
			return 'break';
		
	
		if(!(strpos($link,'collegehumor.com')===false))
			return 'collegehumor';
		
		return '';
	}
	
	
	function parse_query($var)
	{
		$arr  = array();
		
		 $var  = parse_url($var);
		 $varquery=$var['query'];

		 
		 if($varquery=='')
			return $arr;
		
		 $var  = html_entity_decode($varquery);
		 $var  = explode('&', $var);
		 

		foreach($var as $val)
		{
			$x          = explode('=', $val);
			$arr[$x[0]] = $x[1];
		}
		unset($val, $x, $var);
		return $arr;
	}
	
	
	function csv_explode($delim=',', $str, $enclose='"', $preserve=false)
	{
		$resArr = array();
		$n = 0;
		$expEncArr = explode($enclose, $str);
		foreach($expEncArr as $EncItem)
		{
			if($n++%2){
				array_push($resArr, array_pop($resArr) . ($preserve?$enclose:'') . $EncItem.($preserve?$enclose:''));
			}else{
				$expDelArr = explode($delim, $EncItem);
				array_push($resArr, array_pop($resArr) . array_shift($expDelArr));
			    $resArr = array_merge($resArr, $expDelArr);
			}
		}
	return $resArr;
	}
	
	
	function mysqlrealescapestring($inp)
    {
		
		if(is_array($inp))
			return array_map(__METHOD__, $inp);

		if(!empty($inp) && is_string($inp)) {
		    return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
	    }

	    return $inp;

    }	

}




?>