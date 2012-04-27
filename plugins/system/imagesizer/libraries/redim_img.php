<?PHP
/*
	GD-PicEdit V1.2.1
	Copyright (c)2011 reDim - Norbert Bayer	
*/
/*------------------------------------------------------------------------
# redim_img.php for PLG - SYSTEM - IMAGESIZER
# ------------------------------------------------------------------------
# author    reDim - Norbert Bayer
# copyright (C) 2011 redim.de. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.redim.de
# Technical Support:  Forum - http://www.redim.de/kontakt/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class PicEdit {

	private $image=null;
	private $destimage=null;
	public $err=array();

	private $xx=null;
	private $yy=null;
	
	public $grayscale=false;
	
	private $temp_width=null;
	private $temp_height=null;	
	
	public $file=null;
	public $newfile=null;
	
	public $filename=null;
	public $newfilename=null;
	
	public $width=null;
	public $height=null;

	private $factor=0;
	private $mode="";

	public $new_width=null;
	public $new_height=null;

	public $size=null;
	public $type=null;
	public $typename=null;

	public $iptc;

	public $quality=85;
	
	public $bgcolor="000000";


	function __construct($file="",$load=true) {
		
		$this->file=$file;

		if($load==true){
			$this->load();
		}

	}

	function getInstance($file="",$load=true){
		static $instance;
		
		if(!isset($instance)){
			$instance = new PicEdit($file,$load);
		}else{
			if($file!="" and $load==true){
				$instance->load($file);
			}
		}
		
		return $instance;
	}
	
	function reset(){
		
		
	}

	function destroy(){
		if($this->destimage){
			@imagedestroy($this->destimage);
		}
		
		if($this->image){
			@imagedestroy($this->image);
		}		
	}

	function set_factor($width=0,$height=0,$mode=""){

		if($mode!=""){$this->mode=$mode;}
		
				
		$this->xx = $this->width - $width;
		$this->yy = $this->height - $height;
		
		$factor = 0;
				
		$factor1 = @round($this->width / $width, 2);
		$factor2 = @round($this->height / $height, 2);

		switch($this->mode){
			
			case 'cut':
		        if ($factor1 < $factor2) {
		            $this->factor = $factor1;
		        } else {
		            $this->factor = $factor2;
		        }		
			break;
					
			default:
		        if ($factor1 > $factor2) {
		            $this->factor = $factor1;
		        } else {
		            $this->factor = $factor2;
		        }						
			break;
			
		}
	
		
		$factor = $this->factor;

        if ($factor == 0) {
            $this->new_width = $this->width ;
            $this->new_height  = $this->height ;
        } else {
            $this->new_width  = round($this->width / $factor, 0);
            $this->new_height = round($this->height / $factor, 0);
        }
        
		switch ($this->mode){

			case 'cut':
                $this->temp_width = $this->new_width;
                $this->temp_height = $this->new_height;			
                $this->new_width = $width;
                $this->new_height = $height;
			break;
			
			case 'big':
	            if ($this->xx < 0 and $this->yy < 0) {
                	$this->new_width = $this->width;
                	$this->new_height = $this->height;
	            }
			break;
			
			case 'small':
	            if ($this->xx > 0 and $this->yy > 0) {
                	$this->new_width = $this->width;
                	$this->new_height = $this->height;
	            }
			break;

			case 'zoom':
                $this->new_width = $this->width;
                $this->new_height = $this->height;
			break;
			
			case 'equal':
                $this->new_width = $width;
                $this->new_height = $height;
			break;
			
		}


		return array($this->new_width,$this->new_height);		
		
	}

	function create($width=0,$height=0,$mode=null,$newfile="",$quality=null){

		if($quality!=null){$this->quality=$quality;}		 
		if($mode!=null){$this->mode=$mode;}

		switch($this->type){
	      	case '1':
	 	        $this->image = @imagecreatefromgif($this->file);
	        	break;
	
	      	case '2':
	            $this->image = @imagecreatefromjpeg($this->file);
	        	break;
	
	      	case '3':
	            $this->image = @imagecreatefrompng($this->file);
	        	break;				
		}		

	
		if(!$this->image){
		 	$this->err[]="CAN_NOT_CREATE_IMAGE";	
			return false;	
		}
		
		$this->set_factor($width,$height,$mode);

	        
		switch($this->mode){
		 
			
			case 'zoom':
				$this->destimage = @imagecreatetruecolor($width, $height);
				if ($this->bgcolor!=""){
					$rgb=$this->hext_to_rgb($this->bgcolor) ;
			        $bgColor=imagecolorallocate($this->destimage,$rgb['r'],$rgb['g'],$rgb['b']);
			        ImageFilledRectangle($this->destimage, 0, 0, $width, $height, $bgColor); 
				}
				$this->xx=round(($width-$this->new_width)/2);
		        $this->yy=round(($height-$this->new_height)/2);
			break;
					
			default:
		        $this->destimage = @imagecreatetruecolor($this->new_width, $this->new_height);
				$this->xx=0;
		        $this->yy=0;				
			break;
			
		}	
		


		if($this->type==3 OR $this->type==1){
	    
			$transparencyIndex = imagecolortransparent($this->image); 		
	
			if(!isset($transparentIndex)){
				$transparentIndex=0;
			}		
			
			
   			$tc = imagecolorsforindex($this->image, $transparentIndex);
 			
		   	if ($transparencyIndex >= 0)
    		{
				$transparentIndex = @imagecolorallocate($this->image, $tc['red'], $tc['green'], $tc['blue']);
				imagealphablending ( $this->destimage, false );
				imagesavealpha ( $this->destimage, true );
		     	$transparentIndex=	imagecolortransparent($this->destimage, $transparentIndex);  
    		} else {
				$transparentIndex = @imagecolorallocate($this->image, $tc['red'], $tc['green'], $tc['blue']);
				imagealphablending ( $this->destimage, false );
				imagesavealpha ( $this->destimage, true );
		     	$transparentIndex=	imagecolortransparent($this->destimage, $transparentIndex); 
    		}

		}


		if($this->mode=="cut"){

			$pos_x=0;
			$pos_y=0;

			if ($this->temp_width>$this->new_width){
            	$pos_x=round(($this->temp_width-$this->new_width)/2);
            	$this->xx-=$pos_x;
	            $pos_x=0;
            }
			if ($this->temp_height>$this->new_height){
            	$pos_y=round(($this->temp_height-$this->new_height)/2);
            	$this->yy-=$pos_y;
    			$pos_y=0;
            }
            
            $this->new_width = $this->temp_width;
            $this->new_height = $this->temp_height;
		}
		
		if(isset($transparentIndex)){
			ImageFill( $this->destimage, 0,0, $transparentIndex );
		}
			
	     if(function_exists("imagecopyresampled")) {
	        @imagecopyresampled($this->destimage,$this->image, $this->xx, $this->yy, 0, 0, $this->new_width, $this->new_height, $this->width, $this->height);
	     }else{
	        @imagecopyresized($this->destimage, $this->image, $this->xx, $this->yy, 0, 0, $this->new_width, $this->new_height, $this->width, $this->height);
	     }
		
	
		if($newfile!=""){
			return $this->create_file($newfile,true);
		}
		
		return true;
		
	}

	function create_file($file="",$destroy=true){

	 	if($file!=""){$this->newfile=$file;}

		if(!$this->destimage){
			$this->err[]="IMAGE_CREATE_ERROR";
			return false;
		}


		if($this->grayscale==true){
			 imagefilter($this->destimage, IMG_FILTER_GRAYSCALE);	
		}	

		switch($this->type){
		
	        case '1':
	        	#GIF  no PHP-Support $check=@imagejgif($destimage);
	            $check=@imagegif($this->destimage, $this->newfile , $this->quality);
		        break;	
	
	        case '2':
	        	#JPG
	            $check=@imagejpeg($this->destimage, $this->newfile , $this->quality);
	        	break;	
	
	        case '3':
	        	#PNG      		
	            $check=@imagepng($this->destimage, $this->newfile);
	        	break;	
				
		}

		if(!$check){
			$this->err[]="IMAGE_CREATE_ERROR";
			return false;
		}else{
			return true;
		}		
		
		
		if($destroy==true){	$this->destroy(); }
		
		return true;
		
	}

	function hext_to_rgb($hex) {
	    $color = str_replace("#", "", $hex);
	    $rgb = array(
	        "r" => hexdec(substr($color, 0, 2)),
	        "g" => hexdec(substr($color, 2, 2)),
	        "b" => hexdec(substr($color, 4, 2))
	    );
	    return $rgb;
	}
			
	function get_image_size(){

		$imginfo=array();

		if($info = @getimagesize($this->file,$imginfo)){
	#		$this->iptc=$this->get_iptcdata($imginfo);
	#		$exifdata=exif_read_data($file,"",true,false);

		}else{
		 	$this->err[]="CAN_NOT_READ_FILEINFO";
			return false;			
		}
		
		
		$this->filename=basename($this->file);
		$this->width=$info[0];
		$this->height=$info[1];
		$this->type=$info[2];

		switch($this->type){
	      	case '1':
	            $this->typename="gif";
	        	break;
	
	      	case '2':
	            $this->typename="jpg";
	        	break;
	
	      	case '3':
	            $this->typename="png";
	        	break;				
		}	
		
		return true;	
		
	}

	function get_file_size(){

		if(file_Exists($this->file)){
	 	#	$kb=round(filesize($this->file)/1024);
	 		$this->size=filesize($this->file);
		}else{
		 	$this->err[]="FILE_NOT_FOUND";
			return false;
		}	
		
		return true;
		
	}

	function load($file=""){
		
		if($file!=""){ $this->file=$file;}

		if($this->file==""){
			return;
		}

		$ar = parse_url($this->file);
		
		if(!isset($ar["sheme"])){
			$ar["scheme"]="";
		}
		
		if($ar["scheme"]=="http" or $ar["scheme"]=="ftp"){
				#$this->size=filesize($this->file);
		}else{
			
			$this->get_file_size();
		
		}

		$this->get_image_size();
        		
		return true;
	}

	function get_iptcdata($imginfo=null){
		
		if($imginfo==null){
			return false;
		}
	
		$iptc = iptcparse($imginfo['APP13']); 	
		$data=null;

		if (is_array($iptc)) {  

			$data->caption = $iptc["2#120"][0];   
			$data->graphic_name = $iptc["2#005"][0];   
			$data->urgency = $iptc["2#010"][0];   
			$data->category = $iptc["2#015"][0];   
			$data->supp_categories = $iptc["2#020"][0];   
			$data->spec_instr = $iptc["2#040"][0];   
			$data->creation_date = $iptc["2#055"][0];   
			$data->photog = $iptc["2#080"][0];   
			$data->credit_byline_title = $iptc["2#085"][0];   
			$data->city = $iptc["2#090"][0];   
			$data->state = $iptc["2#095"][0];   
			$data->country = $iptc["2#101"][0];   
			$data->otr = $iptc["2#103"][0];   
			$data->headline = $iptc["2#105"][0];   
			$data->source = $iptc["2#110"][0];   
			$data->photo_source = $iptc["2#115"][0];   
			
		}
 	
 		return $data;
		
		
	}


}
