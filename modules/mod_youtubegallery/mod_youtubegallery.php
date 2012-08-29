
<?php
/**
 * youtubegallery Joomla! 2.5 Native Component
 * @version 2.2.4
 * @author DesignCompass corp< <admin@designcompasscorp.com>
 * @link http://www.designcompasscorp.com
 * @license GNU/GPL
 **/


defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.DS.'components'.DS.'com_youtubegallery'.DS.'includes'.DS.'render.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_youtubegallery'.DS.'includes'.DS.'misc.php');




$galleryid=(int)$params->get('galleryid');



$align='';

if($galleryid!=0)
{
	$misc=new YouTubeGalleryMisc;
	$renderer= new YouTubeGalleryRenderer;
								
    
    //Load GALLERY ROW
	$db = & JFactory::getDBO();
				
	$query = 'SELECT * FROM #__youtubegallery WHERE id='.$galleryid.' LIMIT 1';
	$db->setQuery($query);
	if (!$db->query())    die ( $db->stderr());
		
		
	$rows = $db->loadObjectList();
			
	if(count($rows)==0)
		return '';
			
	$row=$rows[0];
	
	$misc->tablerow = &$row;
	
	$firstvideo='';
	
	$youtubegallerycode='';

	$total_number_of_rows=0;
							
	$misc->update_playlist($row);
								
	$videoid=JRequest::getVar('videoid');
	
	if($row->playvideo==1 and $videoid!='')
		$row->autoplay=1;
	
	$videoid_new=$videoid;
	$gallerylist=$misc->getGalleryList_FromCache_From_Table($row->id,$videoid_new,$total_number_of_rows);
	
	if($videoid=='')
	{
		if($row->playvideo==1 and $videoid_new!='')
			JRequest::setVar('videoid',$videoid_new);
	}
	
	$gallerymodule=$renderer->render(
		$gallerylist,
		$galleryid,
		$row,
		$total_number_of_rows
	);

	$app		= JFactory::getApplication();
    
    $align=$params->get( 'galleryalign' );
	
    switch($align)
    {
       	case 'left' :
       		$youtubegallerycode.= '<div style="float:left;position:relative;">'.$gallerymodule.'</div>';
   		break;

		case 'center' :
       		$youtubegallerycode.= '<div style="width:'.$row->width.'px;margin-left:auto;margin-right:auto;position:relative;">'.$gallerymodule.'</div>';
   		break;
        	
       	case 'right' :
      		$youtubegallerycode.= '<div style="float:right;position:relative;">'.$gallerymodule.'</div>';
   		break;
	
       	default :
       		$youtubegallerycode.= $gallerymodule;
   		break;
	
	}//switch($align)
	
	echo $youtubegallerycode;
	
	
}







?>
