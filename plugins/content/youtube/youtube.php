<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//$mainframe->registerEvent( 'onPrepareContent', 'plgYouTube' );

function plgYouTube( &$row, &$params, $page=0 ){
	
	$plugin =& JPluginHelper::getPlugin('content', 'youtube');
	$pluginParams = new JParameter( $plugin->params );

	$regex = '/{youtube}(.*){\/youtube}/iU';

	// check whether plugin has been unpublished
	if ( !$pluginParams->get( 'enabled', 1 ) ) {
		$row->text = preg_replace( $regex, '', $row->text );
		return true;
	}
	
	// find all instances of plugin and put in $matches
	preg_match_all( $regex, $row->text, $matches );
	
	// Number of plugins
	$count = count( $matches[0] );
	
	// plugin only processes if there are any instances of the plugin in the text
 	if ( $count ) {
		for ( $i=0; $i < $count; $i++ )
		{
			$video = renderVideo($matches[1][$i], $pluginParams);
			$row->text 	= preg_replace( $regex, $video, $row->text, 1);
		}
	}
}

function renderVideo($youtubeid, $params) {
	
	$playerType = 'v';
	if (strlen($youtubeid) > 11) $playerType = 'p';
	if (strlen($youtubeid) > 16) $playerType = 'cp';
	
	// get the params for the plugin, and check for color errors/variations
	$related = $params->get('related');
	$color1	= checkColor($params->get('color1'));
	$color2	= checkColor($params->get('color2'));
	$border	= $params->get('border');
	
	$useswfobject = $params->get('useswf');;
	$includeswfobject = $params->get('includeswf');;
	
	// get width/height and adjust accordingly
	$width	= trim($params->get('width'));
	$height	= trim($params->get('height'));
	
	$currentSize = array("width"=>$width, "height"=>$height);
	
	$newSize = getSize($currentSize, $playerType, $border);
	
	//build the querystring that will be appended to the YouTube URL
	$query = $youtubeid;
	$query .= '&rel=' . $related;
	if ($color1 != '') $query .= '&color1=' . $color1;
	if ($color2 != '') $query .= '&color2=' . $color2;
	$query .= '&border=' . $border;
	
	//append $playerType and $query to the URL and return HTML
	
	$html = '';

	if ($useswfobject == 1) {
		
		static $i = 0;
		$i++;
		if ($i == 1 && $includeswfobject == 1){
			$document =& JFactory::getDocument();
			$document->addScript(JURI::root() . 'plugins/content/youtube.js');
		}
		$html .= '<div id="youtube_' . $i . '">&nbsp;</div>';
		$html .= '<script type="text/javascript">';
		$html .= 'var so = new SWFObject("http://www.youtube.com/' . $playerType . '/' . $query . '", "youtube_' . $i . '", "' . $newSize["width"] . '", "' . $newSize["height"] . '", "8", "#ffffff");';
		$html .= 'so.addParam("movie", "http://www.youtube.com/' . $playerType . '/' . $query . '");';
		$html .= 'so.addParam("wmode", "transparent");';
		$html .= 'so.write("youtube_' . $i . '");';
		$html .= '</script>';
	}else{
		$html .= '<object width="' . $newSize["width"] . '" height="' . $newSize["height"] . '">';
		$html .= '<param name="movie" value="http://www.youtube.com/' . $playerType . '/' . $query . '"></param>';
		$html .= '</param><param name="wmode" value="transparent"></param>';
		$html .= '<embed src="http://www.youtube.com/' . $playerType . '/' . $query . '" type="application/x-shockwave-flash" wmode="transparent" width="' . $newSize["width"] . '" height="' . $newSize["height"] . '"></embed>';
		$html .= '</object>';
	}
	
	return $html;

}

function checkColor($color) {
	// Some very basic error checking for the color values
	if ($color != '') {
		$color = str_replace('#', '0x', $color);
		if (strrpos($color, '0x') === false) $color = '0x' . $color;
	}
	return $color;
}

function getSize($size, $player, $border) {
	
	// Set the default YouTube width/height based on playertype
	switch ($player) {
		case "v":
		case "p":
			$width = 425;
			$height = ($border == '0') ? 355 : 373;
			break;
		case "cp":
			$width = 780;
			$height = 445;
			break;
	}
	
	// If both set then return the original values
	if($size["width"] != '' && $size["height"] != ''){
		return($size);
	}

	// If both empty or default YouTube width/height then return default YouTube width/height
	if(($size["width"] == $width && $size["height"] == $height) || ($size["width"] == '' && $size["height"] == '')){
		return(array("width"=>$width, "height"=>$height));
	}
	
	// If only height is set
	if($size["height"] != '') {
		$s =  $size["height"] / $height;
	}
	else { // Else use width
		$s = $size["width"] / $width;
	}
	
	$nw = round($width * $s);
	$nh = round($height * $s);
	
	return(array("width"=>$nw, "height"=>$nh));

}