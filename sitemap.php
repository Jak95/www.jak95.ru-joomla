<?php 
$url="http://".$_SERVER['HTTP_HOST']."/joomla/index.php?option=com_xmap&view=xml&id="; /*Указываем путь до карты сайта, генерируемой компонентом Xmap*/
$xml_code = file_get_contents($url.'1');
if (file_put_contents($_SERVER['DOCUMENT_ROOT'].'/joomla/sitemap.xml', $xml_code))
   {
      echo "<h1>XML sitemap successfully updated</h1>";
      $xml_code = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/joomla/sitemap.xml'); /* файл, в который будет сохранена карта сайта*/
      $xml_code = str_replace ("</url>", "</url><br>", $xml_code);
      echo $xml_code;
	  
	  $xml_code = file_get_contents($url.'2');
	  $xml_code = str_replace ("</url>", "</url><br>", $xml_code);
      echo $xml_code;
	  
	  $xml_code = file_get_contents($url.'3');
	  $xml_code = str_replace ("</url>", "</url><br>", $xml_code);
      echo $xml_code;
	  
   } 
      else echo "<h1>Error!</h1>";
?>