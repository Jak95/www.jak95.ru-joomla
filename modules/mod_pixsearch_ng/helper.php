<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class modPixsearchHelperNG
{
	static function init( $params )
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet( JURI::base().'modules/mod_pixsearch_ng/media/css/mod_pixsearch_ng.css' );

		$settings = new stdClass();
		$settings->searchText = JText::_( 'SEARCH' );
		$settings->nextLinkText = JText::_( 'NEXT' );
		$settings->prevLinkText = JText::_( 'PREV' );
		$settings->viewAllText = JText::_( 'VIEW_ALL' );
		$settings->resultText = JText::_( 'RESULTS' );
		$settings->readMoreText = JText::_( 'READ_MORE' );
		$settings->foundText = JText::_( 'FOUND' );
		$settings->baseUrl = JURI::root();
		$settings->searchType = $params->get( 'searchphrase', 'any' );
		$settings->pagesize = (int)$params->get( 'pagesize', 10 );
		$settings->numsearchstart = (int)$params->get( 'searchstartchar', 4 );
		$settings->use_images = (int)$params->get( 'use_images', 1 );
		$settings->show_read_more = (int)$params->get( 'show_readmore', 1 );

		$document->addScriptDeclaration( 'var ps_ng_settings = '.json_encode( $settings ).';' );
		$document->addScript( JURI::root().'modules/mod_pixsearch_ng/media/js/gpixsearch/gpixsearch.nocache.js' );
	}
}

if (!function_exists('json_encode'))
{
  function json_encode($a=false)
  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
    {
      if (is_float($a))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
    {
      if (key($a) !== $i)
      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)
    {
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
}
?>
