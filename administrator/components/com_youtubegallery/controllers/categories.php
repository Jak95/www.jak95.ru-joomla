<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 
/**
 * Youtube Gallery - Categories Controller
 */
class YoutubeGalleryControllerCategories extends JControllerAdmin
{
        /**
         * Proxy for getModel.
         */
		function display()
		{
				//echo 'sss';
				//die;
				JRequest::setVar( 'view', 'categories');
				parent::display();
		}
		
        public function getModel($name = 'Categories', $prefix = 'YoutubeGalleryModel') 
        {
                $model = parent::getModel($name, $prefix, array('ignore_request' => true));
                return $model;
        }
        
 
 
        public function delete()
        {
                
        	// Check for request forgeries
        	JRequest::checkToken() or jexit( 'Invalid Token' );
        	
            $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

            if (!count($cid)) {

                $this->setRedirect( 'index.php?option=com_youtubegallery&view=categories', JText::_('COM_YOUTUBEGALLERY_NO_CATEGORIES_SELECTED'),'error' );
                
        		return false;
        	}
		
        	$model =&$this->getModel();
        	
        	$model->ConfirmRemove();
        }
	
        public function remove_confirmed()
        {
		
        	// Get some variables from the request
        	
        	$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );


        	if (!count($cid)) {
        		$this->setRedirect( 'index.php?option=com_youtubegallery&view=categories', JText::_('COM_YOUTUBEGALLERY_NO_CATEGORIES_SELECTED'),'error' );
        		return false;
        	}

        	$model =& $this->getModel('categoryform');
        	if ($n = $model->delete($cid)) {
        		$msg = JText::sprintf( 'COM_YOUTUBEGALLERY_CATEGORY_S_DELETED', $n );
        		$this->setRedirect( 'index.php?option=com_youtubegallery&view=categories', $msg );
        	} else {
        		$msg = $model->getError();
        		$this->setRedirect( 'index.php?option=com_youtubegallery&view=categories', $msg,'error' );
        	}
		
        }
		
		public function copyItem()
		{
				
		    $cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
	    
		    $model = $this->getModel('categories');
	    
	    
		    if($model->copyItem($cid))
		    {
				$msg = JText::_( 'COM_YOUTUBEGALLERY_CATEGORY_COPIED_SUCCESSFULLY' );
		    }
		    else
		    {
				$msg = JText::_( 'COM_YOUTUBEGALLERY_CATEGORY_WAS_UNABLE_TO_COPY' );
		    }
	    
		    $link 	= 'index.php?option=com_youtubegallery&view=categories';
		    $this->setRedirect($link, $msg);
		}

}

?>