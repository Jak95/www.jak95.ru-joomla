<?php

/*------------------------------------------------------------------------
# redim.php for PLG - SYSTEM - IMAGESIZER
# ------------------------------------------------------------------------
# author    reDim - Norbert Bayer
# copyright (C) 2011 redim.de. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.redim.de
# Technical Support:  Forum - http://www.redim.de/kontakt/
-------------------------------------------------------------------------*/

# reDim - InfoBox V1.0
// Check to ensure this file is within the rest of the framework
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldreDim extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	public $type  = 'reDim';
	public $_version = '1.6';

	protected function ScanFolder($dir,$ext=0){
		$the_files=array();
		// check if directory exists
		if (is_dir($dir))
		{
			if ($handle = opendir($dir)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != '.' && $file != '..' ) {
						$files[] = $dir .DS.$file;
					}
				}
			}
			closedir($handle);

			foreach ($files as $file)
			{
				if (is_dir($file)){
					if($ext==0){
				 	 	$file=str_replace(DS.DS,DS,$file);
						$ar=JFormFieldreDim::ScanFolder($file,0);
						if (is_array($ar)){
							$the_files=array_merge ($the_files,$ar);			 
						}												

					}
			 	}else{			 	 
			 	 	$file=str_replace(DS.DS,DS,$file);
					$the_files[] = $file;					
				}
			}
			
			unset($files);
		}		
			
			
		return $the_files;	
	}

	protected function getPluginFiles($dir="",$base=""){

		if($base==""){
			$base=$dir;
		}

		$files=JFormFieldreDim::ScanFolder($dir,$base);
		$html='<files>'."\n";
		foreach ($files as $file){
			$file=str_replace($base.DS,"",$file);
		 	$file="   <filename>".$file."</filename>";	 	 
			$html.= $file."\n";
		}
		$html.='</files>'."\n\n\n\n";
				
		$html='<textarea style="width:100%" rows="23" name="S1" cols="51">'.$html.'</textarea>';
		return $html;
	}


	protected function getInput()
	{
	#	$view =  $node->attributes('view');
		$view =  $this->element['view'];
		switch ($view){

		case 'version':

			$dispatcher = JDispatcher::getInstance();
			$info = $dispatcher->trigger('redim_'.$this->element['objname'], array ());
	
			if($info){			
				$info=$info[0];
				$html="";
	            $html.= $info->_name." - Version <b>".$info->_version."</b>";
	            $html='<p>'.$html.'</p>';
			}else{
				$html='-';
			}
		break;


		case 'pluginfiles':
			$html=JFormFieldreDim::getPluginFiles(JPATH_SITE.DS.$this->element['path']);
		break;
		
		case 'infomode':
			$img=JURI::root()."plugins/system/imagesizer/formfields/infomode.png";
			$html='<br style="clear: both" />'.JText::_("IMAGESIZER_INFO_MODE");
			$html.='<br /><img src="'.$img.'" />';
			$html.='<br style="clear: both" /><br />'.JText::_("IMAGESIZER_INFO_COMMAND");
			$html='<div>'.$html.'</div>';
		break;

		case 'updatecheck':

			$dispatcher = JDispatcher::getInstance();
			$info = $dispatcher->trigger('redim_'.$this->element['objname'], array ());
		
			$html="";
			if($info){	
				$info=$info[0];
				
				$ref=rawurlencode(base64_encode(JURI::root()));
				
				
				$url="http://www.redim.de/?code=checkupdate&id=".$info->_id."&version=".rawurlencode($info->_version)."&ref=".$ref;
				$str="";

				$fp = @fopen ($url, "r");
				if ($fp)
				{
					while (!feof($fp))
					{
					 $str.= @fgets($fp, 128);
					}
				}
				@fclose($fp);
				$str=strip_tags($str,"<a><b><i><u><p>");
	          
	            $html='<p>'.$str.'</p>';
            }else{
				$html="-";
			}
		break;



		case 'logo':
		$html='<a href="http://www.redim.de" target=_blank><img src="http://www.redim.de/redim_j_logo.gif" border="0" width="198" height="67"></a><br style="clear:both"/>';
		break;

		case 'updatetext':
		$html='Schauen Sie nach Updates: <a target="_blank" href="http://www.redim.de">redim.de</a><br />';
		$html.="<b>INFO/UPDATE</b> Version: ".$this->_version;
		break;


		case 'news':
		$html='<iframe name="redimnews" src="http://plg.redim.de/" scrolling="no" border="0" frameborder="0" width="210" height="100">
Ihr Browser unterstützt Inlineframes nicht oder zeigt sie in der derzeitigen Konfiguration nicht an.</iframe>';
		break;

		case 'help':
            $html= JText::_("HELP1");
		break;

		}

		return $html;

	}
}