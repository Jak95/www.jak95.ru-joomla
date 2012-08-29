
<?php
/**
 * YoutubeGallery Joomla! 2.5 Native Component
 * @version 2.2.4
 * @author DesignCompass corp< <admin@designcompasscorp.com>
 * @link http://www.designcompasscorp.com
 * @license GNU/GPL
 **/


defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');



class plgContentYoutubeGallery extends JPlugin
{

	public function onContentPrepare($context, &$article, &$params, $limitstart=0) {
		
		
		$count=0;
		$count+=$this->plgYoutubeGallery($article->text,true);
		$count+=$this->plgYoutubeGallery($article->text,false);

	}
	
	function strip_html_tags_textarea( $text )
	{
	    $text = preg_replace(
        array(
          // Remove invisible content
            '@<textarea[^>]*?>.*?</textarea>@siu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',"$0", "$0", "$0", "$0", "$0", "$0","$0", "$0",), $text );
     
		return $text ;
	}
	

	function plgYoutubeGallery(&$text_original, $byId)
	{
		
		$text=$this->strip_html_tags_textarea($text_original);
	
		$options=array();
		if($byId)
			$fList=$this->getListToReplace('youtubegalleryid',$options,$text);
		else
			$fList=$this->getListToReplace('youtubegallery',$options,$text);
			
	
		if(count($fList)==0)
			return 0;
		
		require_once(JPATH_SITE.DS.'components'.DS.'com_youtubegallery'.DS.'includes'.DS.'misc.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_youtubegallery'.DS.'includes'.DS.'render.php');
		
		
	
		for($i=0; $i<count($fList);$i++)
		{
			$replaceWith=$this->getYoutubeGallery($options[$i],$i,$byId);
			$text_original=str_replace($fList[$i],$replaceWith,$text_original);	
		}
	
		return count($fList);
	}



	function getYoutubeGallery($galleryparams,$count,$byId)
	{
		$result='';
		
		$opt=explode(',',$galleryparams);
		if(count($opt)<1)
			return '';
	
		
		$db = & JFactory::getDBO();
		
		if($byId)
		{
			$galleryid=(int)$opt[0];
			$query = 'SELECT * FROM #__youtubegallery WHERE id='.$galleryid.' LIMIT 1';
		}
		else
		{
			$galleryname=trim($opt[0]);
			$query = 'SELECT * FROM #__youtubegallery WHERE galleryname="'.$galleryname.'" LIMIT 1';
		}
			
		
		$db->setQuery($query);
		if (!$db->query())    die ( $db->stderr());
		
		
		$rows = $db->loadObjectList();
				
		if(count($rows)==0)
			return '';
			
		$row=$rows[0];
		$galleryid=$row->id;
		
		
		if(count($opt)>1)
		{
			$row->width=(int)$opt[1];
			
			$row->height=(int)$opt[2];
			$row->playvideo=(int)$opt[3];
			$row->repeat=(int)$opt[4];
			$row->fullscreen=(int)$opt[5];
			$row->autoplay=(int)$opt[6];
			$row->relatedvideos=(int)$opt[7];
			$row->showinfo=(int)$opt[8];
			$row->thumbbgcolor=$opt[9];
			$row->columns=(int)$opt[10];
			
			$row->showtitle=(int)$opt[11];
			
		}
		else
		{

		}
		
	
		if($width<1)
			$width=400;
			
		if($height<1)
				$height=300;
	
		$misc=new YouTubeGalleryMisc;
		$misc->tablerow = &$row;

		$total_number_of_rows=0;
							
		$misc->update_playlist($row);
								
		$videoid=JRequest::getVar('videoid');

		if($row->playvideo==1 and $videoid!='')
			$row->autoplay=1;

		$videoid_new=$videoid;
		$gallerylist=$misc->getGalleryList_FromCache_From_Table($galleryid,$videoid_new,$total_number_of_rows);
							
		if($videoid=='')
		{
			if($row->playvideo==1 and $videoid_new!='')
				JRequest::setVar('videoid',$videoid_new);
		}
			

		$renderer= new YouTubeGalleryRenderer;
		
		$result.=$renderer->render(
								 $gallerylist,
								 $galleryid,
								 $row,
								 $total_number_of_rows
								 );
		$misc->tablerow = &$row;
	
		return $result;
	
	}
	
	

	function getListToReplace($par,&$options,&$text)
	{
		$temp_text=preg_replace("/<textarea\b[^>]*>(.*?)<\/textarea>/i", "", $text);
		
		$fList=array();
		$l=strlen($par)+2;
	
		$offset=0;
		do{
			if($offset>=strlen($temp_text))
				break;
		
			$ps=strpos($text, '{'.$par.'=', $offset);
			if($ps===false)
				break;
		
		
			if($ps+$l>=strlen($temp_text))
				break;
		
		$pe=strpos($text, '}', $ps+$l);
				
		if($pe===false)
			break;
		
		$notestr=substr($temp_text,$ps,$pe-$ps+1);

			$options[]=substr($temp_text,$ps+$l,$pe-$ps-$l);
			$fList[]=$notestr;
			

		$offset=$ps+$l;
		
			
		}while(!($pe===false));
		
		return $fList;
	}
	
}
?>