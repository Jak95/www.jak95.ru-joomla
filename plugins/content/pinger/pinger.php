<?php
/*
Plugin Name: Яндекс.ПДС Пингер
Plugin URI: http://site.yandex.ru/cms-plugins/
Description: Плагин оповещает сервис Яндекс.Поиск для сайта о новых и измененных документах.
Version: 1.0
Author: ООО "ЯНДЕКС"
Author URI: http://www.yandex.ru/
License: GPL2
*/

/*  Copyright 2012 Yandex LLC

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

  (Это свободная программа: вы можете перераспространять ее и/или изменять
   ее на условиях Стандартной общественной лицензии GNU в том виде, в каком
   она была опубликована Фондом свободного программного обеспечения; либо
   версии 2 лицензии, либо (по вашему выбору) любой более поздней версии.

   Эта программа распространяется в надежде, что она будет полезной,
   но БЕЗО ВСЯКИХ ГАРАНТИЙ; даже без неявной гарантии ТОВАРНОГО ВИДА
   или ПРИГОДНОСТИ ДЛЯ ОПРЕДЕЛЕННЫХ ЦЕЛЕЙ. Подробнее см. в Стандартной
   общественной лицензии GNU.

   Вы должны были получить копию Стандартной общественной лицензии GNU
   вместе с этой программой. Если это не так, см.
   <http://www.gnu.org/licenses/>.)
   
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import library dependencies
jimport('joomla.event.plugin');
jimport('joomla.version');

class plgContentPinger extends JPlugin {
	public function onContentAfterSave($context, &$article, $isNew) {
		$articleId = $article -> id;
		$this -> ping($articleId, &$article, &$params);
		return true;
	}

	function get_date($date) {
		$timestamp = strtotime($date);
		$delta = $timestamp - gmmktime();
		return $delta;
	}

	public function onContentChangeState($context, $pks, $value) {
		$articleId = $pks[0];
		$this -> ping($articleId, &$article, &$params);
		return true;
	}

	function ping($articleId, &$article, &$params) {
		$plugin = &JPluginHelper::getPlugin('content', 'pinger');
		$key = $this -> params -> get("yakey");
		$yalogin = $this -> params -> get("yalogin");
		$searchId = $this -> params -> get("yasearchid");
		$pluginId = $this -> params -> get("pluginid");

		//Getting plugin params
		$pluginName = $this -> _name;

		$jv = new JVersion();
		$cmsver = $jv -> getShortVersion();
		//Getting article status
		$database = &JFactory::getDBO();

		$database -> setQuery("SELECT access, publish_up, state FROM #__content WHERE id='$articleId'");
		$articleInfo = $database -> loadAssoc();

		$url = "http://" . $_SERVER['HTTP_HOST'] . "/index.php?option=com_content&view=article&id=$articleId";

		if (($articleInfo['access'] == 1) && ($articleInfo['state'] == 1)) {
			$postdata = http_build_query(array(
				'key' => urlencode($key), 
				'login' => urlencode($yalogin), 
				'search_id' => urlencode($searchId), 
				'pluginid' => urlencode($pluginId), 
				'cmsver' => $cmsver, 
				'publishdate' => $this -> get_date($articleInfo['publish_up']), 
				'urls' => $url
			));

			$host = 'site.yandex.ru';
			$length = strlen($postdata);

			$out = "POST /ping.xml HTTP/1.1\r\n";
			$out .= "HOST: " . $host . "\r\n";
			$out .= "Content-Type: application/x-www-form-urlencoded'\r\n";
			$out .= "Content-Length: " . $length . "\r\n\r\n";
			$out .= $postdata . "\r\n\r\n";

			try {
				$errno = '';
				$errstr = '';
				$result = '';
				$socket = @fsockopen($host, 80, $errno, $errstr, 30);
				if ($socket) {
					if (!fwrite($socket, $out)) {
						throw new Exception("unable to write");
					} else {
						while ($in = @fgets ($socket, 1024)){
		            		$result.=$in;
		           		} 
					}
				} else {
					throw new Exception("unable to create socket");
				}
				fclose($socket);
				$result_xml = array();
				preg_match('/(<.*>)/u', $result, $result_xml);
				if (count($result_xml) && function_exists('simplexml_load_string')) {
					$result = array_pop($result_xml);
					$xml = simplexml_load_string($result);

					if (isset($xml -> error) && isset($xml -> error -> code)) {
						if ($xml -> error -> code) {
							$getInfo = (string)$xml -> error -> code;
							$message = JText::_($getInfo);
							if ($message == "NOT_CONFIRMED_IN_WMC")
								$message = "Сайт не подтвержден в сервисе Яндекс.Вебмастер для указанного имени пользователя.";
							if (($message=="ILLEGAL_VALUE_TYPE")||($message=="SEARCH_NOT_OWNED_BY_USER"))
								$message = "Один или несколько параметров в настройках плагина указаны неверно - ключ (key), логин (login) или ID поиска (searchid).";

						}
					}
					elseif(isset($xml -> invalid)) {
						$getInfo = $xml->invalid->url;
						$reason = $xml->invalid["reason"];
						
						if ($reason=="NOT_CONFIRMED_IN_WMC") $reason = "Сайт не подтвержден в сервисе Яндекс.Вебмастер для указанного имени пользователя.";
						if ($reason=="OUT_OF_SEARCH_AREA") $reason = "Сайт не присутствует в области поиска вашей поисковой площадки. Проверьте настройки области поиска в интерфейсе сервиса Яндекс.Поиск для сайта.";
						if ($getInfo!='') $message= "Невозможно отправить пинг. Причина:".$reason;
						
						$getInfo = $xml->added->url;
						if ($getInfo!='') $message= "Ошибка в работе плагина";
						$getInfo = $xml->error->code;
						if ($getInfo!='') $message= "Ошибка. ".$getInfo;
						if ($message == "") $message="Один или несколько параметров в настройках плагина указаны неверно - ключ (key), логин (login) или ID поиска (searchid)";
					} elseif( isset($xml -> added) 
						&& isset($xml -> added['count']) 
						&& $xml -> added['count'] >0) {
						$message = "Плагин работает корректно";
					}
						
					if(isset($message) && $message) {
                    	$this->params->setValue("message", $message);
                        $paramsString = $this->params->toString();
                        $database->setQuery(sprintf("UPDATE #__extensions SET params=%s WHERE element=%s",
                        $database->Quote($paramsString),
                        $database->Quote($pluginName)
                        ));
                        $database->query();
					}
				}
				return true;
			}
			catch(exception $e){
				return false;
			}
			return false;
		}
    }
}
?>
