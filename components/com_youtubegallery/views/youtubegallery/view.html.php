<?php
/**
 * YoutubeGallery Joomla! 2.5 Native Component
 * @version 2.2.4
 * @author DesignCompass corp< <admin@designcompasscorp.com>
 * @link http://www.designcompasscorp.com
 * @license GNU/GPL
 **/


// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the YoutubeGallery Component
 */
class YoutubeGalleryViewYoutubeGallery extends JView
{
        // Overwriting JView display method
        function display($tpl = null) 
        {
                // Assign data to the view
                
                 // Assign data to the view
                $this->youtubegallerycode = $this->get('YoutubeGalleryCode');
 
                // Check for errors.
                if (count($errors = $this->get('Errors'))) 
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }
                
 
                // Display the view
                parent::display($tpl);
        }
}


?>