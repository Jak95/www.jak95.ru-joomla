<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 
/**
 * Youtube Gallery - GalleryList Controller
 */
class YoutubeGalleryControllerGalleryList extends JControllerAdmin
{
        /**
         * Proxy for getModel.
         */
		function display()
		{
				//echo 'sss';
				//die;
				JRequest::setVar( 'view', 'gallerylist');
				parent::display();
		}
		
        public function getModel($name = 'GalleryList', $prefix = 'YoutubeGalleryModel') 
        {
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
        }
        
 
		public function refreshItem()
		{
				
				$model =&$this->getModel('galleryform');
        	
				
				$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
	    
				if (count($cid)<1) {
		
				       $this->setRedirect( 'index.php?option=com_youtubegallery&view=gallerylist', JText::_('COM_YOUTUBEGALLERY_NO_ITEMS_SELECTED'),'error' );
                
						return false;
				}
					    	    
				if($model->RefreshPlayist($cid))
				{
						$msg = JText::_( 'COM_YOUTUBEGALLERY_GALLERY_REFRESHED_SUCCESSFULLY' );
						$link 	= 'index.php?option=com_youtubegallery&view=gallerylist';
						$this->setRedirect($link, $msg);
				}
				else
				{
						$msg = JText::_( 'COM_YOUTUBEGALLERY_GALLERY_WAS_UNABLE_TO_REFRESHED' );
						$link 	= 'index.php?option=com_youtubegallery&view=gallerylist';
						$this->setRedirect($link, $msg,'error');
				}

		}
 
        public function delete()
        {
                
        	// Check for request forgeries
        	JRequest::checkToken() or jexit( 'Invalid Token' );
        	
            $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

            if (count($cid)<1) {

                $this->setRedirect( 'index.php?option=com_youtubegallery&view=gallerylist', JText::_('COM_YOUTUBEGALLERY_NO_ITEMS_SELECTED'),'error' );
                
        		return false;
        	}
		
        	$model =&$this->getModel();
        	
        	$model->ConfirmRemove();
        }
	
        public function remove_confirmed()
        {
		
        	// Get some variables from the request
        	
        	$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );


        	if (count($cid)<1) {
        		$this->setRedirect( 'index.php?option=com_youtubegallery&view=gallerylist', JText::_('COM_YOUTUBEGALLERY_NO_ITEMS_SELECTED'),'error' );
        		return false;
        	}

        	$model =& $this->getModel('galleryform');
        	if ($n = $model->delete($cid)) {
        		$msg = JText::sprintf( 'COM_YOUTUBEGALLERY_ITEM_S_DELETED', $n );
        		$this->setRedirect( 'index.php?option=com_youtubegallery&view=gallerylist', $msg );
        	} else {
        		$msg = $model->getError();
        		$this->setRedirect( 'index.php?option=com_youtubegallery&view=gallerylist', $msg,'error' );
        	}
		
        }
		
		public function copyItem()
		{
				
		    $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
	    
		    $model = $this->getModel('gallerylist');
	    
	    
		    if($model->copyItem($cid))
		    {
				$msg = JText::_( 'COM_YOUTUBEGALLERY_GALLERY_COPIED_SUCCESSFULLY' );
				$link 	= 'index.php?option=com_youtubegallery&view=gallerylist';
				$this->setRedirect($link, $msg);
		    }
		    else
		    {
				$msg = JText::_( 'COM_YOUTUBEGALLERY_GALLERY_WAS_UNABLE_TO_COPY' );
				$link 	= 'index.php?option=com_youtubegallery&view=gallerylist';
				$this->setRedirect($link, $msg,'error');
		    }
	    
		    
		}

}

?>