<?php
/*
 * v1.0.0
 * Friday Sep-02-2011
 * @component JooComments
 * @copyright Copyright (C) Abhiram Mishra www.bullraider.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JooCaptcha {
 
   var $font = JPATH_SITE;//.DS.'captcha/font.ttf';
   var $width_captch=100;
   var $height_captcha=30;
   var $number_of_characters=6;
   
 function __construct(){
   	$this->font=JPATH_SITE.DS.'components'.DS.'com_joocomments'.DS.'captcha'.DS.'font.ttf';	
   }
   function generateCode($characters) {
      /* list all possible characters, similar looking characters and vowels have been removed */
      $possible = 'ABCDEF23456789bcdfghjkmnpqrstvwxyz';
      $code = '';
      $i = 0;
      while ($i < $characters) { 
         $code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
         $i++;
      }
      return $code;
   }
   
   /**
    * 
    * This function is responsible for storing current captcha code
    * in session.
    */
   function saveCaptchaCode($captchaCode){

   	$_SESSION['comments-captcha-code'] = $captchaCode;
   }
	function check( $code )
	{	$var =0;

		//echo 'what is in session'.$_SESSION['comments-captcha-code'];
		if(($code != '') && ($code == $_SESSION['comments-captcha-code'])){
			return 1;
		}
		return $var;
	}

	function destroy()
	{
		unset($_SESSION['comments-captcha-code']);
	}
   
   function CaptchaSecurityImages($width='100',$height='30',$characters='6') {
   	  $this->destroy();
      $code = $this->generateCode($characters);
      $this->saveCaptchaCode($code);
      /* font size will be 75% of the image height */
      $font_size = $height * 0.60;
      $image = imagecreate($width, $height) or die('Cannot initialize new GD image stream');
      /* set the colours */
      $background_color = imagecolorallocate($image, 255, 255, 255);
      $text_color = imagecolorallocate($image, 255, 40, 100);
      $noise_color = imagecolorallocate($image, 100, 120, 180);
      /* generate random dots in background */
     /* for( $i=0; $i<($width*$height)/3; $i++ ) {
         imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
      }*/
      /* generate random lines in background */
     /* for( $i=0; $i<($width*$height)/150; $i++ ) {
         imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
      }*/
      /* create textbox and add text */
      $textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
      $x = ($width - $textbox[4])/2;
      $y = ($height - $textbox[5])/2;
      imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');
      /* output captcha image to browser */
     $this->applyHeader();
      imagepng($image);
      imagedestroy($image);
   }
 private  function applyHeader(){
 		header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . "GMT" );
		header ( "Cache-Control: no-store, no-cache, must-revalidate" );
		header ( "Cache-Control: post-check=0, pre-check=0", false );
		header ( "Pragma: no-cache" );
		header ('Content-Type: image/png');
 }
}


?>