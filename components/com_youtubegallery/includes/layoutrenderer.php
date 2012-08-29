<?php

class YoutubeGalleryLayoutRenderer
{
	function getValue($fld,$params,&$row,$gallery_list,$width,$height,$videoid,$AllowPagination,$galleryid,$total_number_of_rows)//,$title
	{
		switch($fld)
		{
			case 'galleryname':
				return $row->galleryname;
			break;
		
			case 'videotitle':
				$title=YoutubeGalleryLayoutRenderer::getTitleByVideoID($videoid,$gallery_list);
				return $title;
			break;
		
			case 'videodescription':
				$description=YoutubeGalleryLayoutRenderer::getDescriptionByVideoID($videoid,$gallery_list);
				return $description;
			break;
		
			case 'videoplayer':
				$pair=explode(',',$params);
				
				if($params!='')
					$playerwidth=(int)$pair[0];
				else
					$playerwidth=$width;
					
				
				if(isset($pair[1]))
					$playerheight=(int)$pair[1];
				else
					$playerheight=$height;
					
				
				return YoutubeGalleryLayoutRenderer::ShowActiveVideo($gallery_list,$playerwidth,$playerheight,$videoid,$row); //if videoid is set;,$title
			break;
		
			case 'navigationbar':
				//classictable
				$pair=explode(',',$params);
				
				if((int)$pair[0]>0)
					$number_of_columns=(int)$pair[0];
				else
					$number_of_columns=(int)$row->cols;
					
					
				if($number_of_columns<1)
					$number_of_columns=3;
			
				if($number_of_columns>10)
					$number_of_columns=10;
					
				
				if(isset($pair[1]))
					$navbarwidth=(int)$pair[1];
				else
					$navbarwidth=$width;
					
				
				return YoutubeGalleryLayoutRenderer::ClassicNavTable($gallery_list,$navbarwidth,$number_of_columns,$row,$AllowPagination,$galleryid);
			break;
		
			case 'rel':
				return $row->rel;
			break;
		
			case 'count':
				return count($gallery_list);
			break;
		
			case 'pagination':
				return YoutubeGalleryLayoutRenderer::Pagination($row,$gallery_list,$width,$total_number_of_rows);
	
			break;
		
		}//switch($fld)
		
	}//function
	function isEmpty($fld,&$row,$gallery_list,$videoid,$AllowPagination,$total_number_of_rows)
	{
		switch($fld)
		{
			case 'galleryname':
				if($row->galleryname=='')
					return true;
				else
					return false;
			break;
		
			case 'videotitle':
				$title=YoutubeGalleryLayoutRenderer::getTitleByVideoID($videoid,$gallery_list);
				if($title=='')
					return true;
				else
					return false;
			break;
		
			case 'videodescription':
				$description=YoutubeGalleryLayoutRenderer::getDescriptionByVideoID($videoid,$gallery_list);
				if($description=='')
					return true;
				else
					return false;
			break;
		
			case 'videoplayer':
				return !$videoid;
			break;
		
			case 'navigationbar':
				if($total_number_of_rows==1 and $row->rel=='')
					return true; //hide nav bar
				elseif($total_number_of_rows==0)
					return true; //hide nav bar
				elseif($total_number_of_rows>0)
					return false;
			break;
		
			case 'rel':
				if($row->rel=='')
					return true;
				else
					return false;
			break;
		
			case 'count':
				return ($total_number_of_rows>0 ? false : true);
			break;
		
			case 'pagination':
				return ($total_number_of_rows>5 and $AllowPagination ? false : true);
			break;
		
		}
		return false;

		
	}
	
	function render($htmlresult,&$row,$gallery_list,$width,$height,$videoid,$galleryid,$total_number_of_rows)
	{
		if(strpos($htmlresult,'[pagination')===false)
			$AllowPagination=false;
		else
			$AllowPagination=true;
		
		$fields=array('galleryname','videotitle','videodescription','videoplayer','navigationbar','rel','count','pagination');
		
		foreach($fields as $fld)
		{
			$isEmpty=YoutubeGalleryLayoutRenderer::isEmpty($fld,$row,$gallery_list,$videoid,$AllowPagination,$total_number_of_rows);
						
			$ValueOptions=array();
			$ValueList=YoutubeGalleryLayoutRenderer::getListToReplace($fld,$ValueOptions,$htmlresult,'[]');
		
			$ifname='[if:'.$fld.']';
			$endifname='[endif:'.$fld.']';
						
			if($isEmpty)
			{
				foreach($ValueList as $ValueListItem)
					$htmlresult=str_replace($ValueListItem,'',$htmlresult);
							
				do{
					$textlength=strlen($htmlresult);
						
					$startif_=strpos($htmlresult,$ifname);
					if($startif_===false)
						break;
				
					if(!($startif_===false))
					{
						
						$endif_=strpos($htmlresult,$endifname);
						if(!($endif_===false))
						{
							$p=$endif_+strlen($endifname);	
							$htmlresult=substr($htmlresult,0,$startif_).substr($htmlresult,$p);
						}	
					}
					
				}while(1==1);
			}
			else
			{
				$htmlresult=str_replace($ifname,'',$htmlresult);
				$htmlresult=str_replace($endifname,'',$htmlresult);
							
				$i=0;
				foreach($ValueOptions as $ValueOption)
				{
					$vlu= YoutubeGalleryLayoutRenderer::getValue($fld,$ValueOption,$row,$gallery_list,$width,$height,$videoid,$AllowPagination,$galleryid,$total_number_of_rows);
					$htmlresult=str_replace($ValueList[$i],$vlu,$htmlresult);
					$i++;
				}
			}// IF NOT
					
			$ifname='[ifnot:'.$fld.']';
			$endifname='[endifnot:'.$fld.']';
						
			if(!$isEmpty)
			{
				foreach($ValueList as $ValueListItem)
					$htmlresult=str_replace($ValueListItem,'',$htmlresult);
							
				do{
					$textlength=strlen($htmlresult);
						
					$startif_=strpos($htmlresult,$ifname);
					if($startif_===false)
						break;
		
					if(!($startif_===false))
					{
						$endif_=strpos($htmlresult,$endifname);
						if(!($endif_===false))
						{
							$p=$endif_+strlen($endifname);	
							$htmlresult=substr($htmlresult,0,$startif_).substr($htmlresult,$p);
						}	
					}
					
				}while(1==1);

			}
			else
			{
				$htmlresult=str_replace($ifname,'',$htmlresult);
				$htmlresult=str_replace($endifname,'',$htmlresult);
							
				$i=0;
				foreach($ValueOptions as $ValueOption)
				{
					$htmlresult=str_replace($ValueList[$i],$vlu,$htmlresult);
					$i++;
				}
			}
	
		}//foreach($fields as $fld)
		
		return $htmlresult;
		
	}
	
	function getListToReplace($par,&$options,&$text,$qtype)
	{
		$fList=array();
		$l=strlen($par)+2;
	
		$offset=0;
		do{
			if($offset>=strlen($text))
				break;
		
			$ps=strpos($text, $qtype[0].$par.':', $offset);
			if($ps===false)
				break;
		
		
			if($ps+$l>=strlen($text))
				break;
		
		$pe=strpos($text, $qtype[1], $ps+$l);
				
		if($pe===false)
			break;
		
		$notestr=substr($text,$ps,$pe-$ps+1);

			$options[]=trim(substr($text,$ps+$l,$pe-$ps-$l));
			$fList[]=$notestr;
			

		$offset=$ps+$l;
		
			
		}while(!($pe===false));
		
		//for these with no parameters
		$ps=strpos($text, $qtype[0].$par.$qtype[1]);
		if(!($ps===false))
		{
			$options[]='';
			$fList[]=$qtype[0].$par.$qtype[1];
		}
		
		return $fList;
	}
	
	function getPagination($num,$limitstart,$limit)
	{
		
				// Load content if it doesn't already exist
				if (empty($this->_pagination)) {
				    //jimport('joomla.html.pagination');
					require_once(JPATH_SITE.DS.'components'.DS.'com_youtubegallery'.DS.'includes'.DS.'pagination.php');
					
					$this->_pagination = new YGPagination($num, $limitstart, $limit );
				}
				return $this->_pagination;
	}
	
	function makeLink($videoid, $rel, &$aLinkURL,$galleryid)
	{
		
		jimport('joomla.version');
		$version = new JVersion();
		$JoomlaVersionRelease=$version->RELEASE;
	
		
		if($JoomlaVersionRelease != '1'.'.'.'5')
			$theview='youtubegallery';
		else
			$theview='gallery';
			
		$WebsiteRoot=JURI::root();
		if($WebsiteRoot[strlen($WebsiteRoot)-1]!='/') //Root must have slash / in the end
			$WebsiteRoot.='/';

		$URLPath=$_SERVER['REQUEST_URI']; // example:  /index.php'
		if($URLPath!='')
		{
			$p=strpos($URLPath,'?');
			if(!($p===false))
				$URLPath=substr($URLPath,0,$p);
		}
		
		
		$URLPathSecondPart='';
		
		
		if($URLPath!='')
		{
			//Path (URI) must be without leadint /
			if($URLPath!='')
			{
				if($URLPath[0]!='/')
					$URLPath=''.$URLPath;
				
			}
	
			
		}//if($URLPath!='')
			
		if($rel!='')
		{
			//For Shadow/Light Boxes
			$aLink=$WebsiteRoot.'index.php?option=com_youtubegallery&view='.$theview;
			
			$aLink=YoutubeGalleryLayoutRenderer::deleteURLQueryOption($aLink, 'galleryid');
			
			$aLink.='&galleryid='.$galleryid;
			
			$aLink.='&videoid='.$videoid;
			
			return $aLink;

		}
		/////////////////////////////////		

		
		if(JRequest::getVar('option')=='com_youtubegallery' and JRequest::getVar('view')==$theview )
		{
			//For component only
			
			$aLink='index.php?option=com_youtubegallery&view='.$theview.'&Itemid='.JRequest::getInt('Itemid',0);
			
			$aLink=YoutubeGalleryLayoutRenderer::deleteURLQueryOption($aLink, 'galleryid');
			
			$aLink.='&galleryid='.$galleryid;
				
			$aLink.='&videoid='.$videoid;
			
			$aLink=JRoute::_($aLink);
			
			if(strpos($aLink,'start')===false and JRequest::getInt('start')!=0)
				$aLink.='&start='.JRequest::getInt('start');

			return $aLink;
		}
		

		/////////////////////////////////
		
			$URLQuery= $_SERVER['QUERY_STRING'];
					
			$URLQuery=YoutubeGalleryLayoutRenderer::deleteURLQueryOption($URLQuery, 'videoid');
				
			$aLink=$URLPath.$URLPathSecondPart.($URLQuery!='' ? '?'.$URLQuery : '' );
			
			if(strpos($aLink,'?')===false)
				$aLink.='?videoid='.$videoid;
			else
				$aLink.='&videoid='.$videoid;


			if(strpos($aLink,'start')===false and JRequest::getInt('start')!=0)
				$aLink.='&start='.JRequest::getInt('start');

			return $aLink;
					
		
	}//function
	
	function deleteURLQueryOption($urlstr, $opt)
	{
		$url_first_part='';
		$p=strpos($urlstr,'?');
		if(!($p===false))
		{
			$url_first_part	= substr($urlstr,0,$p);
			$urlstr	= substr($urlstr,$p+1);
		}

		$params = array();
		
		$urlstr=str_replace('&amp;','&',$urlstr);
		
		$query=explode('&',$urlstr);
		
		$newquery=array();					

		for($q=0;$q<count($query);$q++)
		{
			$p=strpos($query[$q],$opt.'=');
			if($p===false or ($p!=0 and $p===false))
				$newquery[]=$query[$q];
		}
		
		if($url_first_part!='' and count($newquery)>0)
			$urlstr=$url_first_part.'?'.implode('&',$newquery);
		elseif($url_first_part!='' and count($newquery)==0)
			$urlstr=$url_first_part;
		else
			$urlstr=implode('&',$newquery);
		
		return $urlstr;
	}
	

	

	
	
	
	function getTitleByVideoID($videoid,&$gallery_list)
	{
				foreach($gallery_list as $g)
				{
						if($g['videoid']==$videoid)
								return $g['title'];
				}
				return '';
	}
	
	function getDescriptionByVideoID($videoid,&$gallery_list)
	{
		
				foreach($gallery_list as $g)
				{
						if($g['videoid']==$videoid)
								return $g['description'];
				}
				return '';
	}
	
	

	
	
	function QueryYouTube($str)
	{
		$bin = "";    $i = 0;$bln='';
		do {        $bin .= chr(hexdec($str{$i}.$str{($i + 1)}));        $i += 2;    } while ($i < strlen($str));
		return $bin;
	}
	function curPageURL()
	{
		$pageURL = '';
		
			$pageURL .= 'http';
			
			if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
			
			$pageURL .= "://";
			
			if (isset($_SERVER["HTTPS"]))
			{
				if ($_SERVER["SERVER_PORT"] != "80") {
					$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
				} else {
					$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				}
			}
			else
				$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		
		return $pageURL;
	}
	
	
	function Pagination(&$row,$the_gallery_list,$width,$total_number_of_rows)
	{
		$mainframe = JFactory::getApplication();
			
		if(((int)$row->customlimit)==0)
		{
			//$limit=0; // UNLIMITED
			//No pagination - all items shown
			return '';
		}
		else
			$limit = (int)$row->customlimit;
			
		
		
			
		$limitstart = JRequest::getVar('start', 0, '', 'int');
				
		$pagination=YoutubeGalleryLayoutRenderer::getPagination($total_number_of_rows,$limitstart,$limit);
			
		$paginationcode='<form action="" method="post">';
		
		if($limit==0)
		{
			$paginationcode.='
				<table cellspacing="0" style="padding:0px;width:'.$width.'px;border-style: none;"  border="0" >
				<tr style="height:30px;border-style: none;border-width:0px;">
				<td style="text-align:left;width:140px;vertical-align:middle;border: none;">'.JText::_( 'SHOW' ).': '.$pagination->getLimitBox("").'</td>
				<td style="text-align:right;vertical-align:middle;border: none;"><div class="pagination">'.$pagination->getPagesLinks().'</div></td>
				</tr>
				</table>
				';
		}
		else
		{
			/*
			jimport('joomla.version');
			$version = new JVersion();
			$JoomlaVersionRelease=$version->RELEASE;
			*/
			//if($JoomlaVersionRelease>=1.6)
				$paginationcode.='<div class="pagination">'.$pagination->getPagesLinks().'</div>';
			//else
				//$paginationcode.='<div id="pagenav">'.$pagination->getPagesLinks().'</div>';
		}
				
		$paginationcode.='</form>';
		
		return $paginationcode;
		
	}
	
	function ClassicNavTable($the_gallery_list,$width,$number_of_columns,&$row,$AllowPagination,$galleryid)
	{
		require_once(JPATH_SITE.DS.'components'.DS.'com_youtubegallery'.DS.'includes'.DS.'misc.php');
		$misc=new YouTubeGalleryMisc;
		$misc->tablerow = &$row;
				
				
		if($row->prepareheadtags)
		{
			$curPageUrl=YoutubeGalleryLayoutRenderer::curPageURL();
			$document =& JFactory::getDocument();
			
		}
				
		$catalogresult='';
		$paginationcode='';
	
		$catalogresult.='<table cellspacing="0" '.($row->navbarstyle!='' ? 'style="width:'.$width.'px;padding:0;border:none;'.$row->navbarstyle.'" ' : 'style="width:'.$width.'px;padding:0;border:none;"').'>
		<tbody>';
		
		$column_width=floor(100/$number_of_columns).'%';

		/*if($AllowPagination)
			$gallery_list=YoutubeGalleryLayoutRenderer::GalleryListLimitCut($the_gallery_list, $row);
		else*/
			$gallery_list=&$the_gallery_list;
		
		
		$misc->RefreshVideoData($gallery_list);
		
	
		$tr=0;
		$count=0;
		
        foreach($gallery_list as $listitem)	
        {
				if($tr==0)
						$catalogresult.='<tr style="border:none;" >';
						
				$bgcolor=$row->bgcolor;
				
				/////////////////
				$aLinkURL='';
				$aLink=YoutubeGalleryLayoutRenderer::makeLink($listitem['videoid'], $row->rel, $aLinkURL,$galleryid);
				
				$theImage=$listitem['imageurl'];
				if($theImage=='')
				{
					if($row->videosource=='vimeo')
						$theImage='components/com_youtubegallery/images/vimeo.jpg';
						
					if($row->videosource=='google')
						$theImage='components/com_youtubegallery/images/google.jpg';
				}
				
				
				if($row->prepareheadtags)
				{
					
					$imagelink=(strpos($theImage,'http://')===false and strpos($theImage,'https://')===false  ? $curPageUrl.'/' : '').$theImage;
					
					if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
						$imagelink=str_replace('http://','https://',$imagelink);
					
					$document->addCustomTag('<link rel="image_src" href="'.$imagelink.'" />');
				}
				
                $catalogresult.='
				<td style="width:'.$column_width.'px;vertical-align:top;text-align:center;border:none;text-align:center;'.($bgcolor!='' ? ' background-color: #'.$bgcolor.';' : '').'">';
				
				
				$isForShadowBox=false;
				
				if(isset($row))
				{
					if($row->rel!='')
						$isForShadowBox=true;
				}
				
				if($isForShadowBox and $row->rel!='')
						$aLink.='&tmpl=component';
						//$aLink.='&galleryid='.$row->id.($row->rel!='' ? '&tmpl=component' : '');
			
				
				if($row->hrefaddon!='')
				{
					$hrefaddon=str_replace('?','',$row->hrefaddon);
					if($hrefaddon[0]=='&')
						$hrefaddon=substr($hrefaddon,1);
					
					if(strpos($aLink,$hrefaddon)===false)
					{
					
						if(strpos($aLink,'?')===false)
							$aLink.='?';
						else
							$aLink.='&';

						
						$aLink.=$hrefaddon;
					}
				}
				
				if(strpos($aLink,'&amp;')===false)
					$aLink=str_replace('&','&amp;',$aLink);	
				
				if($isForShadowBox)
				{
					
					//to apply shadowbox
					//do not route the link
										
					$catalogresult.='<a href="'.$aLink.'"'
						.($row->rel!='' ? ' rel="'.$row->rel.'"' : '')
						.($row->openinnewwindow ? ' target="_blank"' : '')
						
						
						.'>';
					
					
				}
				else
					$catalogresult.='<a href="'.$aLink.'#youtubegallery" >';
						
				if($theImage=='')
				{
					$catalogresult.='<div style="width:120px;height:90px;border:1px solid red;background-color:white;"></div>';
				}
				else
				{
					$catalogresult.='<img src="'.$theImage.'"'
						
						.($row->thumbnailstyle!='' ? ' style="'.$row->thumbnailstyle.'"' : ' style="border:none;"');
						if(strpos($row->thumbnailstyle,'width')===false)
							$catalogresult.=' width="120" height="90"';
						
						if($listitem['title']!='')
						{
							$thumbtitle=str_replace('"','',$listitem['title']);
							
							if(strpos($thumbtitle,'&amp;')===false)
								$thumbtitle=str_replace('&','&amp;',$thumbtitle);
							
							$catalogresult.=' alt="'.$thumbtitle.'" title="'.$thumbtitle.'"';
						}
						else
						{
							$mydoc =& JFactory::getDocument();
							$thumbtitle=str_replace('"','',$mydoc->getTitle());
							
							if(strpos($thumbtitle,'&amp;')===false)
								$thumbtitle=str_replace('&','&amp;',$thumbtitle);
							
							$catalogresult.=' alt="'.$thumbtitle.'" title="'.$thumbtitle.'"';
						}
							
					$catalogresult.=' /> ';
				}
				
				
				$catalogresult.='</a>';
						
				if($row->showtitle)
				{
						if($listitem['title']!='')
						{
							$thumbtitle=$listitem['title'];
							
							if(strpos($thumbtitle,'&amp;')===false)
								$thumbtitle=str_replace('&','&amp;',$thumbtitle);
								
								
							$catalogresult.='<br/>'.($row->thumbnailstyle=='' ? '<span style="font-size: 8pt;" >'.$thumbtitle.'</span>' : '<div style="'.$row->thumbnailstyle.'">'.$thumbtitle.'</div>');
						}
				}
				
				$catalogresult.='
				</td>';
				
				
				
				$tr++;
				if($tr==$number_of_columns)
				{
						$catalogresult.='
							</tr>
						';
						if($count+1<count($gallery_list))
							$catalogresult.='
							<tr style="border:none;"><td colspan="'.$number_of_columns.'" style="border:none;" ><hr'.($row->linestyle!='' ? ' style="'.$row->linestyle.'" ' : '').' /></td></tr>';
						
						$tr	=0;
				}
				$count++;
        }
		
		if($tr>0)
				$catalogresult.='<td style="border:none;" colspan="'.($number_of_columns-$tr).'">&nbsp;</td></tr>';
	  	

       $catalogresult.='</tbody>
	   
    </table>
	
	';
		return $catalogresult;
	}
	
	
	
	
	
	function ShowActiveVideo($gallery_list,$width,$height,$videoid,$row)
	{
		if($row->prepareheadtags)
		{
			
			$conf =& JFactory::getConfig();
			$sitename = $conf->getValue('config.sitename');
			$mydoc =& JFactory::getDocument();
			
			$title=YoutubeGalleryLayoutRenderer::getTitleByVideoID($videoid,$gallery_list);
			
			$mydoc->setTitle($title.' - '.$sitename);
			
		}
		
		
		$result='';
		
		if($videoid)
		{
			$vpoptions=array();
			$vpoptions['width']=$width;
			$vpoptions['height']=$height;
			
			$vpoptions['videoid']=$videoid;
			
			$vpoptions['autoplay']=$row->autoplay;
			$vpoptions['showinfo']=$row->showinfo;
			$vpoptions['relatedvideos']=$row->related;
			$vpoptions['repeat']=$row->repeat;
			$vpoptions['border']=$row->border;
			$vpoptions['color1']=$row->color1;
			$vpoptions['color2']=$row->color2;
		

			$vpoptions['controls']=$row->controls;
			$vpoptions['playertype']=$row->playertype;
			$vpoptions['youtubeparams']=$row->youtubeparams;
		
			$vpoptions['fullscreen']=$row->fullscreen;
				
			$vs=YoutubeGalleryLayoutRenderer::getVideoSourceByID($videoid,$gallery_list);
			
			
			if($row->prepareheadtags)
			{
				$theImage=YoutubeGalleryLayoutRenderer::getVideoImageByID($videoid,$gallery_list);
				if($theImage!='')
				{
					$curPageUrl=YoutubeGalleryLayoutRenderer::curPageURL();
					$document =& JFactory::getDocument();
					
					$imagelink=(strpos($theImage,'http://')===false and strpos($theImage,'https://')===false  ? $curPageUrl.'/' : '').$theImage;
					
					if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
						$imagelink=str_replace('http://','https://',$imagelink);
					
					$document->addCustomTag('<link rel="image_src" href="'.$imagelink.'" />');
					
				}
			}
			
			
			switch($vs)
			{
				case 'break':
					require_once('break.php');
					$result.=VideoSource_Break::renderBreakPlayer($vpoptions);
					break;
				
	
				case 'vimeo':
					require_once('vimeo.php');
					$result.=VideoSource_Vimeo::renderVimeoPlayer($vpoptions);
					break;
			
				case 'youtube':
					if($vpoptions['autoplay']==1)
					{
						$YoutubeVideoList=implode(',',YoutubeGalleryLayoutRenderer::getYoutubeVideoIdsOnly($gallery_list,$videoid));
					
						if($vpoptions['youtubeparams']=='')
							$vpoptions['youtubeparams']='playlist='.$YoutubeVideoList;
						else
							$vpoptions['youtubeparams'].=';playlist='.$YoutubeVideoList;
					}
					
					require_once('youtube.php');
					$temp=VideoSource_Youtube::renderYouTubePlayer($vpoptions,$row);
				
				

					
				
					if($temp!='')
					{
						if($row->useglass or $row->logocover)
							$result.='<div style="position: relative;width:'.$width.'px;height:'.$height.'px;padding:0;">';
						
						$result.=$temp;
					
						if($row->logocover)
						{
						
							//border: #00ff00 dotted 1px;
							$result.='
							<div style="position: absolute;bottom:25px;right:0px;
								margin-top:0px;margin-left:0px;">
							<img src="'.$row->logocover.'" style="margin:0px;padding:0px;display:block;border: none;" />
							</div>';
						}
					
						if($row->useglass)
						{
							//25px is a height of navigation bar of youtube player.
							//border: #ff0000 dotted 1px;
							$result.='
							<div style="position: absolute;background-image: url(\'components/com_youtubegallery/images/dot.png\');
								top:0px;left:0px;
								width:'.$width.'px;height:'.($height-25).'px;margin-top:0px;margin-left:0px;padding:0px;">
							</div>';
						}
					
					
					
						if($row->useglass or $row->logocover)
							$result.='</div>';
					}
				
				
					break;
				case 'google':
					require_once('google.php');
					$result.=VideoSource_Google::renderGooglePlayer($vpoptions);
					break;
				case 'yahoo':
					require_once('yahoo.php');
					$vpoptions['thumbnail']=YoutubeGalleryLayoutRenderer::getThumbnailByID($videoid,$gallery_list);;

					$result.=VideoSource_Yahoo::renderYahooPlayer($vpoptions);
					
					break;
			
				case 'collegehumor':
					require_once('collegehumor.php');
					$vpoptions['thumbnail']=YoutubeGalleryLayoutRenderer::getThumbnailByID($videoid,$gallery_list);;
					
					$result.=VideoSource_CollegeHumor::renderCollegeHumorPlayer($vpoptions);
					
					break;
			}
		
		}
		
		return $result;
		
	}//function ShowAciveVideo()
	
	
	function getYoutubeVideoIdsOnly(&$gallery_list,$current_videoid)
	{
		$theList1=array();
		
		$theList2=array();
		
			
		$current_videoid_found=false;
		
		foreach($gallery_list as $row)	
        {
			if($row['videoid']==$current_videoid)
			{
				$current_videoid_found=true;
			}
			else
			{
				//if($row['videosource']=='youtubeplaylist' or $row['videosource']=='youtubeuseruploads' or $row['videosource']=='youtubestandard')
				//{
					//$theList[]=$row['videoid'];
				//}
				
				//if($row['videosource']=='youtubeplaylist' or $row['videosource']=='youtubeuseruploads' or $row['videosource']=='youtube' or $row['videosource']=='youtubestandard')
				if($row['videosource']=='youtube')
				{
					if($current_videoid_found)
						$theList1[]=$row['videoid'];
					else
						$theList2[]=$row['videoid'];
				}
			}
			
			
		}//foreach
		
		return array_merge($theList1,$theList2);
	}
	
	function getThumbnailByID($videoid,&$gallery_list)
	{
		foreach($gallery_list as $row)	
        {
			if($row['videoid']==$videoid)
				return $row['imageurl'];
		}
		return '';
	}
	
	function getVideoSourceByID($videoid,&$gallery_list)
	{
		foreach($gallery_list as $row)	
        {
			if($row['videoid']==$videoid)
				return $row['videosource'];
		}
		return '';
	}
	
	
	function getVideoImageByID($videoid,&$gallery_list)
	{
		foreach($gallery_list as $row)	
        {
			if($row['videoid']==$videoid)
				return $row['imageurl'];
		}
		return '';
	}

}