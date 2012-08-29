<?php
/**
 * YouTubeGallery Joomla! 2.5 Native Component
 * @version 2.2.4
 * @author DesignCompass Corp <admin@designcompasscorp.com>
 * @link http://www.designcompasscorp.com
 * @license GNU/GPL
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');
function YouTubeGalleryBuildRoute(&$query) {

       $segments = array();
       if(isset($query['view']))
       {
                $segments[] = $query['view'];
                unset( $query['view'] );
       }
	   
	   /*if(isset($query['videoid']))
       {
                $segments[] = $query['videoid'];
                unset( $query['videoid'] );
       }*/

       return $segments;


}
function YouTubeGalleryParseRoute($segments) {

  $vars = array();
       /*switch($segments[0])
       {
               //case 'youtubegallery':
                    //   $vars['view'] = 'youtubegallery';
					  // $vars['videoid'] = $segments[1];
                       break;
              
       }*/
       return $vars;


}
?>