<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * Categories Model
 */
class YoutubeGalleryModelCategories extends JModelList
{
        /**
         * Method to build an SQL query to load the list data.
         *
         * @return string  An SQL query
         */
        protected function getListQuery()
        {
                // Create a new query object.         
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                // Select some fields
                $query->select('id,categoryname');
                // From the Youtube Gallery table
                $query->from('#__youtubegallery_categories');
                return $query;
        }
    
        
       	function ConfirmRemove()
        {
		
		$cancellink='index.php?option=com_youtubegallery&view=categories';
		
		//$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		
                
		if(count($cids)==0)
			return false;
		
	
		//Get Table Name
		
		if (count( $cids ))
		{
			echo '<p>'.JText::_( 'COM_YOUTUBEGALLERY_DELETE_CATEGORY_S' ).': ID='.(count($cids)>1 ? implode(',',$cids) : $cids[0] ).' <a href="'.$cancellink.'">'.JText::_( 'COM_YOUTUBEGALLERY_NO_CANCEL' ).'</a></p>';
		

            echo '
            <form action="index.php?option=com_youtubegallery" method="post" >
            <input type="hidden" name="task" value="categories.remove_confirmed" />
            ';
            $i=0;
            foreach($cids as $cid)
            {
                echo '<input type="hidden" id="cb'.$i.'" name="cid[]" value="'.$cid.'">';
            }
            
            echo '
            <input type="submit" value="'.JText::_( 'COM_YOUTUBEGALLERY_YES_DELETE' ).'" class="button" />
            </form>
		';
		}
		else
		{
			
			echo '<p><a href="'.$cancellink.'">'.JText::_( 'COM_YOUTUBEGALLERY_NO_CATEGORIES_SELECTED' ).'</a></p>';
		}
		
		
		
        }
		
		public function getTable($type = 'Categories', $prefix = 'YoutubeGalleryTable', $config = array()) 
        {
                return JTable::getInstance($type, $prefix, $config);
        }
		
		function copyItem($cid)
		{


				$item =& $this->getTable('categories');
				
	    
		
				foreach( $cid as $id )
				{
			
		
						$item->load( $id );
						$item->id 	= NULL;
		
						$old_title=$item->categoryname;
						$new_title='Copy of '.$old_title;
		
						$item->categoryname = $new_title;
			
	
		
						if (!$item->check()) {
							return false;
						}
		
						if (!$item->store()) {
							return false;
						}
						$item->checkin();
							
				}//foreach( $cid as $id )
		
				return true;
		}//function copyItem($cid)
    
      
}
