<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 
/**
 * YoutubeGallery - CategoryForm Controller
 */
class YoutubeGalleryControllerCategoryForm extends JControllerForm
{
       /**
         * Proxy for getModel.
         */
    
	function save()
	{
		$task = JRequest::getVar( 'task');
		
		// get our model
		$model = &$this->getModel('categoryform');
		// attempt to store, update user accordingly
		
		if($task != 'save' and $task != 'apply')
		{
			$msg = JText::_( 'COM_YOUTUBEGALLERY_CATEGORY_WAS_UNABLE_TO_SAVE');
			$this->setRedirect($link, $msg, 'error');
		}
		
		
		if ($model->store())
		{
		
			if($task == 'save')
				$link 	= 'index.php?option=com_youtubegallery&view=categories';
			elseif($task == 'apply')
			{
	
				
				$link 	= 'index.php?option=com_youtubegallery&view=categoryform&layout=edit&id='.$model->id;
			}
			
			$msg = JText::_( 'COM_YOUTUBEGALLERY_CATEGORY_SAVED_SUCCESSFULLY' );
			
			$this->setRedirect($link, $msg);
		}
		else
		{
			
			$msg = JText::_( 'COM_YOUTUBEGALLERY_CATEGORY_WAS_UNABLE_TO_SAVE');
			$this->setRedirect($link, $msg, 'error');
		}
			
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelItem()
	{
		

		$model = $this->getModel('item');
		$model->checkin();

		$this->setRedirect( 'index.php?option=com_youtubegallery&controller=gallerylist');
	}

	/**
	* Cancels an edit operation
	*/
	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_youtubegallery&controller=gallerylist');
	}

	/**
	* Form for copying item(s) to a specific option
	*/
}
