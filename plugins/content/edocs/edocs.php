<?php

/*
// MediaEventi "Embed Documents" plugin for Joomla! 1.5.x - Version 1.1
// License: http://www.gnu.org/copyleft/gpl.html
// Copyright (c) 2010 MediaEventi.
// More info at http://www.mediaeventi.it
// Developer: Giuseppe Gallo
*/

?>

<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin');
//$mainframe->registerEvent( 'onPrepareContent', 'plgContentedocs' );

function plgContentedocs(&$row, &$params, $page=0 ) {
	// simple performance check to determine whether bot should process further
	$db =& JFactory::getDBO();
	// simple performance check to determine whether bot should process further
	//if ( JString::strpos( $row->text, 'component' ) === false ) { 		return true; 	}
	// define the regular expression for the bot
	$plugin =& JPluginHelper::getPlugin('content', 'edocs');
	$regex = "#{edocs}(.*?){/edocs}#s";
    $pluginParams = new JParameter( $plugin->params );


	// check whether mambot has been unpublished
	if ( !$pluginParams->get( 'enabled', 1 ) ) {
	$row->text = preg_replace( $regex, '', $row->text );
    return true;
     }

	// perform the replacement	
	$row->text = preg_replace_callback( $regex, 'botComponentCode_replacerEdocs', $row->text );	
	return true;
}


function botComponentCode_replacerEdocs( &$matches ) {
global $mainframe;
global $database ;
	$query = "SELECT params"
	. "\n FROM #__plugins"
	. "\n WHERE element = 'edocs'"
	. "\n AND folder = 'content'"
	;

	// Connecting to database...
	$database=&JFactory::getDBO();
	$database->setQuery( $query );
	$plugin =& JPluginHelper::getPlugin('content', 'edocs');
 	$pluginParams = new JParameter( $plugin->params );
	
	$ie_compatibility = $pluginParams->get( 'ie_compatibility', '' );
	$ie_text = $pluginParams->get( 'ie_text', '' );
	$download_text = $pluginParams->get( 'download_text', 'Download' );
	$debug = $pluginParams->get( 'debug', '' );
	$root = $pluginParams->get( 'root', '' );
	if ($root[0] == '/')
		$root = substr($root, 1);
	if (substr($root, -1) == '/')
		$root = substr($root, 0, -1);
	
	$LiveSite = JURI::base();

	// Separating arguments and preparing various parameters
	$arguments = explode(',',$matches[1]);
	$path = $arguments[0];
	$width = $arguments[1];
	$height = $arguments[2];
	@$download = $arguments[3];
	@$div_id = $arguments[4];

	//Building the complete path to the document 
	if(!stristr($path,"http")) {
		if(!($path[0] == '/'))
			$path = $LiveSite . $root . '/' . $path;
		else {
			$path = substr($path, 1);  
			$path = $LiveSite. $path;
		}
	}
		
	//Download link
	if($download == "link") {
		$download_link = '<a href="' . $path . '" target="_blank" class="edocs_link"><span class="edocs_link_text">' . $download_text . '</span></a>';
	}
	else $download_link = "";
	
	if($div_id)
		$div_id = 'id="' . $div_id . '"';

	//Code to display the embedded document
	if($ie_compatibility && (ae_detect_ie())) {
		//$linkname = ucfirst(str_replace("_", " ", ShowFileExtension($path)));
		$info = pathinfo($path);
		$linkname =  ucfirst(str_replace("_", " ", basename($path,'.'.$info['extension']))) ;

		$code = '<div class="edocs_viewer"><a href="http://docs.google.com/viewer?url=' . $path . '" target="_blank" onclick="NewWindow(this.href,\'mywin\',\''. $width . '\',\'' 
					. $height . '\',\'no\',\'center\');return false" onfocus="this.blur()">' . $linkname . ' (' . $info['extension'] . 
					') <br /><span style="font-size: 90%">' . $ie_text .'</span></a></div>';
	
	}
	else 		
		$code = '	<div class="edocs_viewer" '. $div_id .'>
						<iframe 
							src="http://docs.google.com/gview?url=' . $path . '&embedded=true" 
							style="width:' . $width . 'px; height:' . $height . 'px;" frameborder="0" class="edocs_iframe">
						</iframe>
						<br /><br />
						' . $download_link . '
					</div>';
				
	//Debug mode on: print on screen the url of the document
	if($debug)
		$code = $code . '<br /><div style="background: orange; color: #005dff; padding: 5px;"><span style="color: black; font-weight: bold;">Edocs - Debug mode on</span><br /><span style="color: black;">The document path is:</span> ' . $path . '</div><br /><br />';
				
	return $code;
}

function ae_detect_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}
?>

