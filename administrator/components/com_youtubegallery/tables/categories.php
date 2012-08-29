<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
 
/**
 * Youtube Gallery Table class
 */
class YoutubeGalleryTableCategories extends JTable
{
        /**
         * Constructor
         *
         * @param object Database connector object
         */
       	var $id = null;
        var $categoryname = null;
       
        function __construct(&$db) 
        {
                parent::__construct('#__youtubegallery_categories', 'id', $db);
        }

}

?>