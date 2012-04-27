<?php
/*------------------------------------------------------------------------
# PLG - SYSTEM - IMAGESIZER
# ------------------------------------------------------------------------
# author    reDim - Norbert Bayer
# copyright (C) 2011 redim.de. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.redim.de
# Technical Support:  Forum - http://www.redim.de/kontakt/
-------------------------------------------------------------------------*/

# reDim - Norbert Bayer
# Plugin: ImageSizer for Joomla! 1.6/1.7
# license GNU/GPL   www.redim.de

# 
# Thanks to Martin Skroch
#
# Version 1.6 Stable
#
# Version 1.6.1 - 06.12.2011
# eregi entfernt

# Version 1.6.2 - 11.01.2011
# Verschiedene kleine Fehler behoben.


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
define('_IMAGESIZER_IS_LOAD',true);
jimport( 'joomla.plugin.plugin' );

class plgSystemimagesizer extends JPlugin {

	public $_redim_id="61";
	public $_redim_name="ImageSizer";
	public $_redim_version="1.6.2";

	public $error=array();
	public $created_pics=0;
	public $counter=0;
	public $article=NULL;

	private $load_java="";


	public function redim_imagesizer(){

		$dat->_id=$this->_redim_id;
		$dat->_name=$this->_redim_name;	
		$dat->_version=$this->_redim_version;
		
		return $dat;
		
	}


	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

    }

	public function onContentBeforeSave($context, &$article, $isNew)
	{

		if(isset($article->introtext)){
			$article->introtext = preg_replace_callback('/\{(imagesizer)\s*(.*?)\}/i',array($this,"imagesizer_cmd"), $article->introtext);
		}
		if(isset($article->fulltext)){
			$article->fulltext = preg_replace_callback('/\{(imagesizer)\s*(.*?)\}/i',array($this,"imagesizer_cmd"), $article->fulltext);
		}
		if(isset($article->text)){
			$article->text = preg_replace_callback('/\{(imagesizer)\s*(.*?)\}/i',array($this,"imagesizer_cmd"), $article->text);
		}
		
	}

	public function onContentAfterSave($context, &$article, $isNew)
	{
		$app = JFactory::getApplication();
		$this->loadLanguage("",JPATH_ROOT);

	 	$text="";

	 	if(isset($article->introtext)){
			$text.=$article->introtext;
		}
	 
	 	if(isset($article->fulltext)){
			$text.=$article->fulltext;
		}

	 	if(isset($article->description)){
			$text.=$article->description;
		}

		$tmptext=$text;
		if($this->params->get("deltc",1)==1){
			$text=strip_tags($text,"<img>");
		    $regex="/\<img (.*?)\>/i";
		    $text=preg_replace_callback($regex,array($this,"imagesizer_del"),$text);
		}

		if($this->params->get("generate",2)!=2){
			return true;
		}
		
		
		$text=$tmptext;

		/*
		if(isset($article->text)){
			$text=strip_tags($article->text,"<img>");
		}elseif(isset($article->content)){
			$text=strip_tags($article->content,"<img>");	
		}else{
			$text=strip_tags($article->introtext.$article->fulltext,"<img>");			
		}
		*/
		$text=strip_tags($text,"<img>");
			
	    $regex="/\<img (.*?)\>/i";
	    $text=preg_replace_callback($regex,array($this,"imagesizer"),$text);	    
		unset($text);

		if($this->created_pics>0){
			$app->enqueueMessage(JText::sprintf('IMAGESIZER_X_IMAGES_CREATED',$this->created_pics));
		}

		return true;
	}
	
	private function Includefiles(){
		
		if(defined("imagesizer_filesload")){
			return;
		}
		jimport( 'joomla.html.parameter' );
		define("imagesizer_filesload",1);
	
		$file=$this->params->get("lbscript","");
		if($file=="-1" or $file==""){$file="default.php";}
		$file=JPATH_SITE.DS."plugins".DS."system".DS."imagesizer".DS."lbscripts".DS.$file;

		if(file_exists($file)){
			include_once($file);
		}		
		
	}

	private function send_helpdata($email="",$text=""){

		if(empty($email)){
			$user = JFactory::getUser();
			$email = $user->get("email");	
		}


		jimport('joomla.mail.helper');
		
	 	if(!JMailHelper::isEmailAddress($email)){
			return false;
		}

		$ar=array();
		$config =& JFactory::getConfig();

		#echo $config->getValue('sitename');

		$ar["redim_id"]=$this->_redim_id;
		$ar["redim_name"]=$this->_redim_name;
		$ar["redim_version"]=$this->_redim_version;
		$ar["joomla"]=JVERSION;
		$ar["site"]=JURI::root(false);
		$ar["file"]=__FILE__;	
		
		$ar["ftp_enable"]=$config->getValue('ftp_enable',"");
		
		$e='picclass,linkclass,lbscript,generate,generate2,deltc,insert,minsizex,minsizey,maxsizex,maxsizey,modus,chmod,pro';
		
		$e=explode(",",$e);
		foreach($e as $k){
			$ar[$k]=$this->params->get($k,"");
		}
		
		$ar["thumbspath"]=$this->params->get("thumbspath","");
		$ar["chmod"]=JPath::clean(JPATH_SITE.DS.$ar["thumbspath"]);
		$ar["chmod"]=substr(decoct(fileperms($ar["chmod"])),1);
		
		if ( is_writeable (JPATH_SITE.DS.$ar["thumbspath"] ) ){
			$ar["dirwriteable"]="yes";	
		}else{
			$ar["dirwriteable"]="no";			
		}
		
		$body="";
		$body.=$text."\n\n\n";
		
		foreach($ar as $k => $v){
			$body.=$k.": ".$v."\n";	
		}


		jimport('joomla.mail.mail');
		// Create a JMail object
		$mail		= JMail::getInstance();
		$mail->IsHTML(false);
		$mail->addRecipient( "support@redim.de" );	
		$mail->setSender( array( $email , $email ) );
		$mail->addReplyTo( array( $email , $email ) );

		$title="Help&Support: ".$this->_redim_name." - ".$this->_redim_id;
	
		$mail->setSubject($title);
	#	$body=$this->sethtmlheader($title,$body);

		$mail->setBody( $body );
		
		return $mail->Send();	

	}

	public function onAfterInitialise()
	{

		$app = JFactory::getApplication();

		if ($app->getName() == 'administrator') {
			if(JRequest::getCMD("code","")=="redim-helper"){
				$user = JFactory::getUser();
				$lang = JFactory::getLanguage();
				$lang->load('plg_system_imagesizer', JPATH_ADMINISTRATOR);
				if($user->id>0){
					$email=JRequest::getVAR("email","");
					$text=JRequest::getVAR("text","");		
					
					if($this->send_helpdata($email,$text)){
						echo JText::_("IMAGESIZER_HELP_EMAIL_ISSEND"); 
					}else{
						echo JText::_("IMAGESIZER_HELP_EMAIL_NOTSEND");					
					}
				
					die();
				}
			}
		}
	
	
		if($this->params->get("generate2","prepare")!="render"){
			return;
		}

		if ($app->getName() != 'site') {
			return true;
		}


		$this->Includefiles();

	}

	public function onAfterDispatch()
	{

		if($this->params->get("insert","0")!="1"){
			return;
		}

		$app = JFactory::getApplication();

		if ($app->getName() == 'site') {
			return true;
		}

		$document   =& JFactory::getDocument();
		
		if(!isset($document->_scripts[JURI::root(true)."/media/media/js/popup-imagemanager.js"])){
			return true;
		}

		unset($document->_scripts[JURI::root(true)."/media/media/js/popup-imagemanager.js"]);

		$java='var imagesizer2_x='.$this->params->get("minsizex",122).';';
		$java.=' var imagesizer2_y='.$this->params->get("minsizey",122).';';	

		$document->addScript(JURI::ROOT().'plugins/system/imagesizer/js/popup-imagemanager.js');
		$document->addScriptDeclaration($java);		
		unset($java);

#		JHtml::_('script','plugins/system/imagesizer/js/popup-imagemanager.js', true, true);

	}

	private function redim_support(){

		if(defined("redim_support") or !defined("imagesizer_filesload")){
			return;
		}

		if($this->params->get("supportredim","1")!="1" ){
			return;
		}
		
	    define("redim_support",1);
		
		$c="\n".base64_decode('PG5vc2NyaXB0PjwhLS0gcmVEaW0gLSBJbWFnZVNpemVyIGZvciBKb29tbGEgLS0+PGEgdGl0bGU9Ikpvb21sYSAtIFBsdWdpbnMiIGhyZWY9Imh0dHA6Ly93d3cucmVkaW0uZGUvIj5JbWFnZVNpemVyIGZvciBKb29tbGEhIENvcHlyaWdodCAoYykyMDExIHJlRGltPC9hPjwvbm9zY3JpcHQ+')."\n";
		

		$buffer = JResponse::getBody();
		if($buffer = preg_replace('/<\/body>(?!.*<\/body>)/is',$c.'$0',$buffer,1)){	JResponse::setBody($buffer);}
		unset($buffer,$c);
 		

	 }

	public function onAfterRender(){

		$app = JFactory::getApplication();
			 	
		if ($app->getName() != 'site') {
			return true;
		}
	
		
		$this->redim_support();		
						 		
		if($this->params->get("generate2","prepare")!="render"){
			return;
		}
	
		$buffer = JResponse::getBody();
		$this->_imagesizer_preg($buffer);
		JResponse::setBody($buffer);
			 				
		unset($buffer);
	}
	
	public function onContentPrepare($context, &$row, &$params, $page = 0){

		if($this->params->get("generate2","prepare")!="prepare"){
			return;
		}

 	#   $regex="/\<img (.*?)\>/i";
	#	$regex="/\<a (.*?)>(.*?(?=<img ).*?)\<\/a>/i";
	#	$regex="/(?=<a )\<img (.*?)\>/i";
		if(!isset($row->id)){
			$row->id=$this->counter;
		}


		$this->article=$row;

		if(isset($row->text)){
			$this->_imagesizer_preg($row->text);			
		}
		if(isset($row->introtext)){

			$this->_imagesizer_preg($row->introtext);			
		}
		if(isset($row->fulltext)){
			$this->_imagesizer_preg($row->fulltext);			
		}	

		$this->counter++;
		
	}		
	
	private function _imagesizer_preg(&$text){

#		$regex="/\<a (.*?)>(.*?(?=\<img ).*?)\<\/a>/i";
		$regex="/\<a (.*?)>(.*?)\<\/a>/i";
		$text = preg_replace_callback($regex,array($this,"imagesizer"),$text);

	    $regex="/\<img (.*?)\>/i";
	    $text = preg_replace_callback($regex,array($this,"imagesizer"),$text);	    
		$text = preg_replace("/<#img /i","<img ",	$text );

#		$text = preg_replace_callback('/\{(imagesizer)\s*(.*?)\}/i',array($this,"imagesizer_cmd"), $text);
		
	}
	
	private function imagesizer_cmd(&$matches){
		
		if(!isset($matches[2])){
			return $matches[0];
		}

		jimport('joomla.filesystem.file');

		$p=$this->match_to_params($matches[2]);
		
		$path=JPath::clean(trim($p->get("path","")));
		$limit=$p->get("limit","");
		$limit=explode(",",$limit);
		if(isset($limit[1])){
			$start=$limit[0];
			$limit=$limit[1];
		}else{
			$start=0;
			$limit=(int) $limit[0];
		}

		if(substr($path,-1,1)==DS){
			$path=substr($path,0,-1);
		}

		$files	= JFolder::files(JPATH_SITE.DS.$path, '\.png$|\.gif$|\.jpg$|\.PNG$|\.GIF$|\.JPG$',false,false);
        $LiveSite = JURI::root();
        
		$imagesizer2_x=$this->params->get("minsizex",120);
		$imagesizer2_y=$this->params->get("minsizey",120);
		$imgs=array();

		$ii=0;
		foreach($files as $i => $file){
			
			if( ($i>=$start and $ii<$limit) or $limit==0){
			
				if($info = @getimagesize(JPATH_SITE.DS.$path.DS.$file)){
				
					if(count($info)>2){
						
						$ii++;
	
			            if ($info[0] > $imagesizer2_x OR $info[1] > $imagesizer2_y){
			
			                $faktor = 0;
			
			   				if ($info[0]>$info[1] || $info[0]==$info[1]){
			   					$faktor = $info[0] / $imagesizer2_x ;
			   				}
			
			    			if ($info[0]<$info[1]){
			   					$faktor =  $info[1] / $imagesizer2_y ;
			   				}
			
			                if ($faktor>0){
			                   $xx = round( $info[0] / $faktor , 0);
			                   $yy = round( $info[1] / $faktor , 0);
			                }
						}else{
							$xx=$info[0];
							$yy=$info[1];
						}
		 
		 
						$imgs[]='<img src="'.$LiveSite.str_replace("\\","/",$path.DS.$file).'" width="'.$xx.'" height="'.$yy.'" />';
		
					}			
				
				}
				
			}
			
			
		}

		if(count($imgs)){
			return implode("",$imgs);
		}
		
		return $matches[0];

	}
	
	private function imagesizer_del(&$matches){
        $LiveSite = JURI::root();

		$this->Includefiles();

		$thumb_path=JPath::clean(JPATH_ROOT.DS.$this->params->get("thumbspath","cache"));
        $thumb_url=JPath::clean(DS.$this->params->get("thumbspath","cache").DS);

		$ar=$this->make_arrays($matches[1],'/([a-zA-Z0-9._-]+)="(.*?)"/');	

		$file=urldecode($ar["src"]);
		$filename = str_replace(DS,"_",$file);	

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$ch=$thumb_path.DS.$ar["width"]."x".$ar["height"]."_";
		$ch2=$thumb_path.DS.$this->params->get("maxsizex",800)."x".$this->params->get("maxsizey",800)."_";
		

		$files	= JFolder::files($thumb_path, $filename,false,true);

		foreach($files as $k => $file){
			if(strpos(" ".$file,$ch)>0){
				unset($files[$k]);
			}elseif(strpos(" ".$file,$ch2)>0){
				unset($files[$k]);				
			}
		}

		if(count($files)>0){
			if(JFile::delete($files)){
			 	$app = JFactory::getApplication();
				$app->enqueueMessage(JText::sprintf('IMAGESIZER_X_IMAGES_DELETED',count($files)));	
			}
		}

	}

	private function calc_size($ar,$info){
		
		$ar["width"]=trim($ar["width"]);
		$ar["height"]=trim($ar["height"]);

		if(substr($ar["width"],-1,1)=="%"){
			$ar["width"]=@round(($info[0]/100)*intval($ar["width"]));
		}

		if(substr($ar["height"],-1,1)=="%"){
			$ar["height"]=@round(($info[1]/100)*intval($ar["height"]));			
		}		
		
		$ar["width"]=intval($ar["width"]);
		$ar["height"]=intval($ar["height"]);
		
		if($ar["width"]>0 and $ar["height"]==0){
			$factor=@round($info[0] / $ar["width"], 2);
			$ar["height"]= @round($info[1] / $factor, 0);
			unset($factor);
		}elseif($ar["width"]==0 and $ar["height"]>0){		
			$factor=@round($info[1] / $ar["height"], 2);	
			$ar["width"]= @round($info[0] / $factor, 0);
			unset($factor);			
		}

		return $ar;		

	}

	private function check_imgparams($ar){

		$ar["ext"]="";


		if(empty($ar["src"])){
			return $ar;
		}

		$url=parse_url($ar["src"]);

		if(isset($url["path"])){
			$ar["src"]=$url["path"];
		}

		if(isset($url["scheme"])){
			$url["scheme"]="http";
		}

		if(substr($ar["src"],0,1)!="/"){
			$ar["src"]="/".$ar["src"];
		}
		
		if(strtolower(substr($ar["src"],0,11))=="/templates/"){
			return $ar;		
		}

		if(isset($url["host"])){

			if(substr($ar["src"],0,1)=="/"){
				$ar["src"]=substr($ar["src"],1);
			}		 

			if(substr($url["host"],-1,1)!="/"){
				$url["host"]=$url["host"]."/";
			}		 
			
			$url2=parse_url(JURI::root());
			if(strtolower($url2["host"]."/")!=strtolower($url["host"])){
				$ar["ext"]=$url["scheme"]."://".$url["host"].$ar["src"];
			}
			unset($url2);
		}

		unset($url);
		return $ar;
	}

	private function checkmode_from_class($class){

		$class=" ".$class." ";

	#	$mode="equal";

		if ( strpos ( $class , ' imgcut ' ) )
		{
		 	return "cut";
		}
		
		if ( strpos ( $class , ' imgzoom ' ) )
		{
		 	return "zoom";
		}
		
		if ( strpos ( $class , ' imgbig ' ) )
		{
		 	return "big";
		}
		
		if ( strpos ( $class , ' imgsmall ' ) )
		{
		 	return "small";
		}

		return "equal";
		
	}

	
	private function clean_url($var){

	    $var = str_replace('&amp;', '&', $var);
	    $var = str_replace('&lt;', '<', $var);
	    $var = str_replace('&gt;', '>', $var);
	    $var = str_replace('&euro;', '€', $var);
	    $var = str_replace('&szlig;', 'ß', $var);
	    $var = str_replace('&uuml;', 'ü', $var);
	    $var = str_replace('&Uuml;', 'Ü', $var);
	    $var = str_replace('&ouml;', 'ö', $var);
	    $var = str_replace('&Ouml;', 'Ö', $var);
	    $var = str_replace('&auml;', 'ä', $var);
	    $var = str_replace('&Auml;', 'Ä', $var);
	    
		return $var;
		
	}

	private function imagesizer(&$matches){

		$sharpit=false;

		if(count($matches)>2){
			if(isset($matches[2])){
				$ar=$this->make_arrays($matches[2],'/([a-zA-Z0-9._-]+)="(.*?)"/');
				$sharpit=true;
			}else{
				return $matches[0];
			}
		}else{
			$ar=$this->make_arrays($matches[1],'/([a-zA-Z0-9._-]+)="(.*?)"/');			
		}
		
		if(!isset($ar["src"])){
			return $matches[0];
		}

        $LiveSite = JURI::root();

		$this->Includefiles();

		$thumb_path=JPath::clean(JPATH_ROOT.DS.$this->params->get("thumbspath","cache").DS);
        $thumb_url=JPath::clean(DS.$this->params->get("thumbspath","cache").DS);

		$output=array();

		$ar["width"]=intval($ar["width"]);
		$ar["height"]=intval($ar["height"]);

		$ar=$this->check_imgparams($ar);
		
		if(empty($ar["src"])){
			return $matches[0];
		}else{
			$ar["src"]=$this->clean_url($ar["src"]);
		}

		if(empty($ar["width"]) AND empty($ar["height"])){
			return $matches[0];
		}
	
		if(isset($ar["class"])){
			$ar["class"].=" ".$this->params->get("picclass","thumb");
		}else{
			$ar["class"]=$this->params->get("picclass","thumb");	
		}
		$ar["class"]=trim($ar["class"]);

		$mode=$this->checkmode_from_class($ar["class"]);

		if(!empty($ar["ext"])){	
			$ar["src"]=$ar["ext"];			
		}
		
		$url_array = parse_url($ar["src"]);

		$ar["src"]=str_replace(JURI::base(true),"",$ar["src"]);
		$ar["href"]=JURI::base(true).$ar["src"];

		if(!empty($ar["ext"])){
			$info=@getimagesize($ar["ext"]);
		}else{
			$info=@getimagesize(JPath::clean(JPATH_ROOT.DS.$ar["src"]));			
		}

		$ar=$this->calc_size($ar,$info);

		if($ar["width"]==$info[0] AND $ar["height"]==$info[1]){
			return $matches[0];
		}

		if($info[0]<2 AND $info[1]<2){
			return $matches[0];
		}

		if(isset($this->article->id)){
			$id=intval($this->article->id);
		}else{
			$id=0;
		}
		
		if($id==0){	$id="i".JRequest::getINT("Itemid");	}

		#$file=$ar["src"];

		if(!empty($ar["ext"])){
			$file=urldecode($ar["ext"]);
		}else{		
			$file=urldecode($ar["src"]);
		}
		$filename = str_replace("\\","_",$file);	
		$filename = str_replace("/","_",$filename);	
		$filename = str_replace(":","_",$filename);	
		#$filename = $id."_".$ar["width"]."x".$ar["height"]."_".$filename;

		$maxx=$this->params->get("maxsizex",800);
		$maxy=$this->params->get("maxsizey",800);
		$maxfile = JPath::clean(DS.$thumb_url.DS.$maxx."x".$maxy."_".$filename);

		if(substr($maxfile,0,1)=="/" OR substr($maxfile,0,1)=="\\"){
			$maxfile=substr($maxfile,1);
		}

		$filename = $ar["width"]."x".$ar["height"]."-".$mode.$filename;

		if(empty($ar["ext"])){	 
			$file=JPath::clean(JPATH_ROOT.DS.$file);
		}

		$thumbfile=JPath::clean($thumb_path.$filename);

		include_once(JPATH_SITE.DS."plugins".DS."system".DS."imagesizer".DS.'libraries'.DS."redim_img.php");
	#	include_once(JPATH_SITE.DS."plugins".DS."system".DS."imagesizer".DS.'libraries'.DS."ThumbLib.inc.php");

		if(!file_exists($thumbfile)){

			$img = new PicEdit($file);
			#$img = PicEdit::getInstance($file);
					
			if($img->create($ar["width"],$ar["height"],$mode,$thumbfile)){	
						
				$this->created_pics++;			
		        if ($this->params->get('chmod',0)!=0){
		          @chmod($thumbfile,base_convert($this->params->get('chmod',"0774"), 8, 10));
		        }
	        }
	        unset($mode);
		}else{
			#$img = PicEdit::getInstanceReset();
			$img = new PicEdit();
		}



		if(file_exists(JPATH_ROOT.DS.$maxfile)){
			$ar["href"]=$maxfile;
			$img->file=JPATH_ROOT.DS.$maxfile;
		}else{

			if($info[0]>$maxx or $info[1]>$maxy){
			 	if($img==NULL){
					$img = new PicEdit($file);
			 	}else{
					$img->load($file);
				}
				$f=JPATH_ROOT.DS.$maxfile;
				if($img->create($maxx,$maxy,"big",$f)){	
				 	$img->file=JPATH_ROOT.DS.$maxfile;
				 	$ar["href"]=$maxfile;
					$this->created_pics++;	
			        if ($this->params->get('chmod',0)!=0){
			          @chmod(JPATH_ROOT.DS.$maxfile,base_convert($this->params->get('chmod',"0774"), 8, 10));
			        }	
		
			        $ar["href"]=$maxfile;
				}
				unset($f);				
			}
			
		}


		if(isset($img->err)){
			$this->get_error();
		}

		$temp_src=$ar["src"];
		$ar["src"]=$thumb_url.$filename;
		if(substr($ar["src"],0,1)=="/" OR substr($ar["src"],0,1)=="\\"){
			$ar["src"]=substr($ar["src"],1);
		}
		
		# Backslash ändern
		$ar["src"]=str_replace("\\","/",$ar["src"]);
		$ar["href"]=str_replace("\\","/",$ar["href"]);

		if(strpos(" ".$ar["class"],"nolightbox")>0){
			$sharpit=true;
		}


		if($sharpit==true){
			$output=$this->onlythumb($ar,$img);
			if(substr($temp_src,0,1)=="/"){
				$temp_src=substr($temp_src,1);
			}
			$output=str_replace($temp_src,$ar["src"],$matches[0]);
			$output = preg_replace("/<img /i","<#img ",	$output );
		}else{
			$output=ImageSizer_addon_GetImageHTML($ar,$img,$this);				
		}
			
		unset($img);

        return $output;
		
	}

	private function get_error($errors=array()){
		
		if(count($errors)>0){

			foreach($errors as $k => $err){
				JError::raiseNotice($k,$err);		
			}
			
		}
		return true;
		
	}

	private function get_ReadmoreImageHTML($ar=array(),$img){

		$output=plgSystemimagesizer::make_img_output($ar);

		if(isset($ar["title"])){
			$title=' title="'.$ar["title"].'"';
		}else{
			$title="";
		} 

		$output='<a class="'.trim($this->params->get("linkclass","linkthumb")).'" target="_self" title="'.$ar["title"].'" href="'.$ar["href"].'"><img '.$output.' /></a>';	
	
		return $output;
		
	}

	private function onlythumb(&$ar,&$img){

		$output=plgSystemimagesizer::make_img_output($ar,true);
			
		return $output;
	
	}

	public function make_img_output($ar,$protect=false){

		$output=array();

		foreach($ar as $key => $value){
		 
		 	if(trim($value)!=""){
		 	 
				switch($key){
					
					case 'href':
					case 'owidth':
					case 'oheight':
					break;
					
					default:
					$output[]=$key.'="'.$value.'"';
					break;
				}
			 
			}
		}
		$output=implode(" ",$output);

		return $output;
	}

	public function make_arrays($matches,$regex='/([a-zA-Z0-9._-]+)=[\'\"](.*?)[\'\"]/'){
 			
 		$ar=array();
 		$matches2=array();
 
        preg_match_all($regex, $matches, $matches2);
				
        foreach($matches2[1] as $key => $value) {
            $value=trim($value);
            if (isset($ar[strtolower($value)])){
				$value=strtolower($value);
			}
            $ar[$value]=$matches2[2][$key];
        }
        
 		if (isset($ar["style"])){
			$ar2=plgSystemimagesizer::Get_WH_From_Style($ar["style"]);
			if (isset($ar2["width"])){$ar["width"]=$ar2["width"];}
			if (isset($ar2["height"])){$ar["height"]=$ar2["height"];}
			unset($ar2);
		}       
		
 		if (isset($ar["width"])){	
        	$ar["width"]=intval($ar["width"]);
        }else{
			$ar["width"]="";
		}
    
 		if (isset($ar["height"])){	
        	$ar["height"]=intval($ar["height"]);
        }else{
			$ar["height"]="";
		}
        
		return $ar;
	}


	private function Get_WH_From_Style($style){
		$style.=";";
		
		$matches=array();
		$ar=array();

		$regex='/(width|height):(.*?)(\;)/i';
 		preg_match_all($regex, $style, $matches);

		foreach($matches[1] as $key => $value) {
			if (isset($matches[2][$key])){
			 	$matches[2][$key]=trim($matches[2][$key]);
			 	if(substr($matches[2][$key],-1,1)!="%"){
					$k=strtolower(trim($value));
					$ar[$k]=trim($matches[2][$key]);
				}
			}
		}		
	
		return $ar;
	}



	private function match_to_params($match){

		$ar=array();
		$ar["style"]="";
		$m=array();
		$str="";
		
		preg_match_all('/(.*?)=(.*?)[\'\"](.*?)[\'\"]/', $match, $m);	
		
		if (count($m[1])>0){
			foreach($m[1] as $key => $value) {
				$ar[strtolower(trim($value))]=$m[3][$key];
			   	$str.=strtolower(trim($value))."=".$m[3][$key]."\n";
			   
			}
		}
		
		preg_match_all("/(.*?)=(.*?)[\'\"](.*?)[\'\"]/", $match, $m);

		if ($ar["style"]!=""){
			$b=plgSystemimagesizer::Get_WH_From_Style($ar["style"]);
			if(count($b)>0){
				foreach($b as $key => $value) {
				 $m[1][$key]=$key;
				 $m[3][$key]=$value;
				}				
			}
		}

		if (count($m[1])>0){
			foreach($m[1] as $key => $value) {
			  	 $ar[strtolower(trim($value))]=$m[3][$key];
				 $str.=strtolower(trim($value))."=".$m[3][$key]."\n";
			}

		}

		$params = new JParameter($str);
		$params->img_data=$ar;

		return $params;  		
	}


	public function get_imagesrc($file,$width=0,$height=0,$modus="big",$updatecache=false){
		
		if($width>0 and $height==0){
			$height=$width;
		}elseif($width==0 and $height>0){
			$width=$height;
		}
		
		$l=strlen(JPATH_SITE);
		if(substr($file,0,$l)==JPATH_SITE){
			$file=substr($file,$l);
		}
		$typename = substr(strrchr($file,'.'),1);
		
	 	$newfile=JApplication::getHash($file.$width."x".$height."-".$modus).".".$typename;
		$c=substr(strtolower($newfile),1,1);
		$c="cache".DS.$c;
		if(!file_exists(JPATH_SITE.DS.$c)){
			mkdir(JPATH_SITE.DS.$c);
		}
		
		$newfile=$c.DS.$newfile;

		if(!file_exists(JPATH_SITE.DS.$newfile) or $updatecache==true){
		 	if($width>0 and $height>0){
				include_once(JPATH_SITE.DS."plugins".DS."system".DS."imagesizer".DS.'libraries'.DS."redim_img.php");
				$img= new PicEdit(JPATH_SITE.DS.$file);
				$img->create($width,$height,$modus,JPATH_SITE.DS.$newfile);
				unset($img);
			}else{
				$newfile=$file;
			}
		}

		return $newfile;
		
	}

}
