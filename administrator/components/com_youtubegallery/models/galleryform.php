<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * YoutubeGallery Model
 */
class YoutubeGalleryModelGalleryForm extends JModelAdmin
{
        /**
         * Returns a reference to the a Table object, always creating it.
         *
         * @param       type    The table type to instantiate
         * @param       string  A prefix for the table class name. Optional.
         * @param       array   Configuration array for model. Optional.
         * @return      JTable  A database object
         
         */
		public $id;
		
		
        public function getTable($type = 'YoutubeGallery', $prefix = 'YoutubeGalleryTable', $config = array()) 
        {
                return JTable::getInstance($type, $prefix, $config);
        }
        /**
         * Method to get the record form.
         *
         * @param       array   $data           Data for the form.
         * @param       boolean $loadData       True if the form is to load its own data (default case), false if not.
         * @return      mixed   A JForm object on success, false on failure
         
         */
        public function getForm($data = array(), $loadData = true) 
        {
                // Get the form.
                $form = $this->loadForm('com_youtubegallery.galleryform', 'galleryform', array('control' => 'jform', 'load_data' => true)); //$loadData
                if (empty($form)) 
                {
                        return false;
                }
                return $form;
        }
		
		/**
         * Method to get the script that have to be included on the form
         *
         * @return string       Script files
         */
        public function getScript() 
        {
                return 'administrator/components/com_youtubegallery/models/forms/galleryform.js';
        }
		
        /**
         * Method to get the data that should be injected in the form.
         *
         * @return      mixed   The data for the form.
         
         */
        protected function loadFormData() 
        {
                // Check the session for previously entered form data.
				//$data = (array)JFactory::getApplication()->getUserState('com_youtubegallery.edit.galleryform.data', array());
                $data = JFactory::getApplication()->getUserState('com_youtubegallery.edit.galleryform.data', array());
                if (empty($data)) 
                {
                        $data = $this->getItem();
                }
                return $data;
        }
		
		function RefreshPlayist($cids)
		{
				$where=array();
				
				foreach($cids as $cid)
						$where[]= 'id='.$cid;
				
				require_once(JPATH_SITE.DS.'components'.DS.'com_youtubegallery'.DS.'includes'.DS.'misc.php');
				
				 // Create a new query object.         
                
				$db = JFactory::getDBO();
                $query = $db->getQuery(true);
                // Select some fields
                $query->select('*');
                // From the Youtube Gallery table
                $query->from('#__youtubegallery');
				
				if(count($where)>0)
						$query->where(implode(' OR ',$where));
								
				$db->setQuery($query);
				if (!$db->query())    die( $db->stderr());
                
				$rows=$db->loadObjectList();
				if(count($rows)<1)
						return false;
				
				foreach($rows as $row)
				{
						$misc=new YouTubeGalleryMisc;
						$misc->tablerow = &$row;
						$misc->update_cache_table($row,true); 
				
						$query='UPDATE #__youtubegallery SET `lastplaylistupdate`="'.date( 'Y-m-d H:i:s').'" WHERE `id`='.$row->id;
						$db->setQuery($query);
						if (!$db->query())    die( $db->stderr());
						
						//Clear Update Info for each video in this gallery
						$query='UPDATE #__youtubegallery_videos SET `lastupdate`="0000-00-00 00:00:00" WHERE `isvideo` AND `galleryid`='.$row->id;
						$db->setQuery($query);
						if (!$db->query())    die( $db->stderr());
						
						
				}
				
				return true;
		}
        

        function store()
        {
                
                
        	$row =& $this->getTable('youtubegallery');
            

            
        	// consume the post data with allow_html
        	$data_ = JRequest::get( 'post',JREQUEST_ALLOWRAW);
            $data=$data_['jform'];
            
        	$post = array();
            
            $galleryname=trim(preg_replace("/[^a-zA-Z0-9_]/", "", $data['galleryname']));
            
            $data['jform']['galleryname']=$galleryname;
            
           

        	if (!$row->bind($data))
        	{
                
        		return false;
        	}
               
        	// Make sure the  record is valid
        	if (!$row->check())
        	{
                
        		return false;
        	}
				
				if($row->id!=0)
				{
						require_once(JPATH_SITE.DS.'components'.DS.'com_youtubegallery'.DS.'includes'.DS.'misc.php');
						$misc=new YouTubeGalleryMisc;
						$misc->tablerow = &$row;
						$misc->update_cache_table($row); 
						$row->lastplaylistupdate =date( 'Y-m-d H:i:s');
				}						
						
						
        	// Store
        	if (!$row->store())
        	{
                
        		return false;
        	}
				
        	$this->id=$row->id;
			
		//die;
				
				
				
        	return true;
        }
        
    		
		function delete($cids)
        {

        	$row =& $this->getTable('youtubegallery');

            $db = & JFactory::getDBO();
            
        	if (count( $cids ))
        	{
        		foreach($cids as $cid)
        		{
						
				
				if (!$row->delete( $cid ))
				{
					return false;
				}
			}
        	}
		
		
		
        	return true;
        }
}
