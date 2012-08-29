<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

jimport( 'joomla.application.menu' );


 
/**
 * YoutubeGallery Model
 */
class YoutubeGalleryModelYoutubeGallery extends JModelItem
{
        /**
         * @var string msg
         */
        protected $youtubegallerycode;
 
        /**
         * Returns a reference to the a Table object, always creating it.
         *
         * @param       type    The table type to instantiate
         * @param       string  A prefix for the table class name. Optional.
         * @param       array   Configuration array for model. Optional.
         * @return      JTable  A database object
         
         */
		
        public function getTable($type = 'YoutubeGallery', $prefix = 'YoutubeGalleryTable', $config = array()) 
        {
                return JTable::getInstance($type, $prefix, $config);
        }
        /**
         * Get the message
         * @return actual youtube galley code
         */
        public function getYoutubeGalleryCode() 
        {
				jimport('joomla.version');
				$version = new JVersion();
				$JoomlaVersionRelease=$version->RELEASE;
		
				
				$result='';
				
				$app		= JFactory::getApplication();
				$params	= $app->getParams();
				 
                if (!isset($this->youtubegallerycode)) 
                {
                        
						//if(JRequest::getVar('galleryid_'))
								$galleryid=JRequest::getInt('galleryid');
						//else
		                  //      $galleryid=JRequest::getInt('galleryid');
						
						$videoid=JRequest::getVar('videoid');
						
                       
                        // Get a YoutubeGallery instance
                        $row = $this->getTable('YoutubeGallery');
 
                        // Load the data
                        $row->load($galleryid);
 
					
                        //Build flash movie
                        if($galleryid!=0)
                        {
								
								require_once(JPATH_SITE.DS.'components'.DS.'com_youtubegallery'.DS.'includes'.DS.'misc.php');
								require_once(JPATH_SITE.DS.'components'.DS.'com_youtubegallery'.DS.'includes'.DS.'render.php');
	
								$misc=new YouTubeGalleryMisc;
								$renderer= new YouTubeGalleryRenderer;
								
								$misc->tablerow = &$row;
								
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
								
                               
                                $align=$params->get( 'align' );
								
								
                                switch($align)
                                {
                                	case 'left' :
                                		$this->youtubegallerycode = '<div style="float:left;">'.$gallerymodule.'</div>';
                                		break;
        	
                                	case 'center' :
                                		$this->youtubegallerycode = '<div style="width:'.$row->width.'px;margin-left:auto;margin-right:auto;">'.$gallerymodule.'</div>';
                                		break;
        	
                                	case 'right' :
                                		$this->youtubegallerycode = '<div style="float:right;">'.$gallerymodule.'</div>';
                                		break;
	
                                	default :
                                		$this->youtubegallerycode = $gallerymodule;
                                		break;
	
                                }

                        
                        } //if($galleryid!=0)
                        
                }
				
				
				
				if($params->get( 'allowcontentplugins' ))
				{
								$o = new stdClass();
								$o->text=$this->youtubegallerycode;
							
								$dispatcher	= JDispatcher::getInstance();
							
								JPluginHelper::importPlugin('content');
							
								$r = $dispatcher->trigger('onContentPrepare', array ('com_content.article', &$o, &$params_, 0));
							
								$this->youtubegallerycode=$o->text;
				}
				
				$result.=$this->youtubegallerycode;
				
				
                return $result;
        }
}
