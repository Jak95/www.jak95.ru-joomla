<?php
/*
 * v1.0.2
 * Friday Sep-02-2011
 * @component JooComments
 * @copyright Copyright (C) Abhiram Mishra www.bullraider.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
 
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class Helper{
	
	public static function languageString($str){
		if(strpos( $str, '-' )!== false ){
			$splited=explode("-",$str);
			$splited[1]=strtoupper($splited[1]);
			return implode("-", $splited);
		}else{
			return $str;
		}
	}
}