<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
 
/**
 * Youtube Gallery Table class
 */
class YoutubeGalleryTableYoutubeGallery extends JTable
{
        /**
         * Constructor
         *
         * @param object Database connector object
         */
       	var $id = null;
        var $galleryname = null;
        var $gallerylist = null;
	
        var $width = null;
        var $height = null;
        var $playvideo = null;
        var $repeat = null;
        var $fullscreen = null;
        var $autoplay = null;
        var $related = null;
        var $showinfo = null;
        var $bgcolor = null;
        var $cols = null;
        var $showtitle = null;

        var $cssstyle = null;
        var $navbarstyle = null;
        var $thumbnailstyle = null;
        var $linestyle = null;
	
        var $showgalleryname = null;
        var $gallerynamestyle = null;
        var $showactivevideotitle = null;
        var $activevideotitlestyle = null;
	
        var $description = null;
        var $descr_position = null;
        var $descr_style = null;
	
	
        var $color1 = null;
        var $color2 = null;
	
        var $border = null;
        
        var $openinnewwindow = null;
        var $rel = null;
        var $hrefaddon = null;
	
		var $pagination = null;
		var $customlimit = null;
		
		var $catid = null;
		var $controls = null;
		var $youtubeparams = null;
		var $playertype = null;

		var $useglass = null;
		var $logocover = null;

		var $customlayout = null;
		var $randomization = null;
		var $prepareheadtags = null;
		
		var $updateperiod = null;
		
		var $muteonplay = null;
		
		var $lastplaylistupdate = null;
		
		var $volume = null;

        function __construct(&$db) 
        {
                parent::__construct('#__youtubegallery', 'id', $db);
        }
}

?>