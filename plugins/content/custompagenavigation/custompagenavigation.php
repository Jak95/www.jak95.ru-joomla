<?php

/**
 * @package Plugin Custom Page Navigation for Joomla! 1.5
 * @version $Id: custompagenavigation.php 456 2011-02-22 19:00:30 $
 * @author E-max
 * @copyright Copyright (C) 2011 - E-max
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 **/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
error_reporting(E_ALL);

class plgContentCustomPageNavigation extends JPlugin
{
	/**
	 * @since	1.6
	 */
	public function onContentBeforeDisplay($context, &$row, &$params, $page=0)
	{
		$view = JRequest::getCmd('view');
		$print = JRequest::getBool('print');
		echo '<link rel="stylesheet" href="'.JURI::base(true).'/plugins/content/custompagenavigation/style/style.css" type="text/css" media="all" />';

		if (!property_exists($row, 'text')) {
			return false;
		}
		
		$ok = strstr ($row->text, '{loadnavigation}');
		
		if ($print) {
			return false;
		}

		if (($context == 'com_content.article') && $ok) {
			$html = '';
			$db		= JFactory::getDbo();
			$user	= JFactory::getUser();
			$nullDate = $db->getNullDate();

			$date	= JFactory::getDate();
			$config	= JFactory::getConfig();
			$now	= $date->toMySQL();

			$uid	= $row->id;
			$option	= 'com_content';
			$canPublish = $user->authorise('core.edit.state', $option.'.'.$view.'.'.$row->id);

			// Determine sort order
			switch ($this->params->get('ordering')) {
				case 'created_asc':
					$orderBy = 'a.created';
				break;
				case 'created_dsc':
					$orderBy = 'a.created DESC';
				break;
				case 'title_az':
					$orderBy = 'a.title ASC';
				break;
				case 'title_za':
					$orderBy = 'a.title DESC';
				break;
				case 'popular_first':
					$orderBy = 'a.hits DESC';
				break;
				case 'popular_last':
					$orderBy = 'a.hits ASC';
				break;
				case 'ordering_fwd':
					$orderBy = 'a.ordering ASC';
				break;
				case 'ordering_rev':
					$orderBy = 'a.ordering DESC';
				break;
				case 'id_asc':
					$orderBy = 'a.id';
				break;
				case 'id_dsc':
					$orderBy = 'a.id DESC';
				break;
				default:
					$orderBy = 'a.ordering ASC';
				break;
			}

			$xwhere = ' AND (a.state = 1 OR a.state = -1)' .
			' AND (publish_up = '.$db->Quote($nullDate).' OR publish_up <= '.$db->Quote($now).')' .
			' AND (publish_down = '.$db->Quote($nullDate).' OR publish_down >= '.$db->Quote($now).')';

			// Array of articles in same category correctly ordered.
			$query	= $db->getQuery(true);
			$query->select('a.id, a.title, '
					.'CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug, '
					.'CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
			$query->from('#__content AS a');
			$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
			$query->where('a.catid = '. (int)$row->catid .' AND a.state = '. (int)$row->state
						. ($canPublish ? '' : ' AND a.access = ' .(int)$row->access) . $xwhere);
			$query->order($orderBy);

			$db->setQuery($query);
			$list = $db->loadObjectList('id');

			// This check needed if incorrect Itemid is given resulting in an incorrect result.
			if (!is_array($list)) {
				$list = array();
			}

			reset($list);

			// Location of current content item in array list.
			$location = array_search($uid, array_keys($list));

			$rows = array_values($list);

			$row->prev = null;
			$row->next = null;

			if ($location -1 >= 0)	{
				// The previous content item cannot be in the array position -1.
				$row->prev = $rows[$location -1];
			}

			if (($location +1) < count($rows)) {
				// The next content item cannot be in an array position greater than the number of array postions.
				$row->next = $rows[$location +1];
			}

			$pnSpace = "";
			if (JText::_('JGLOBAL_LT') || JText::_('JGLOBAL_GT')) {
				$pnSpace = " ";
			}

			if ($row->prev) {
				$row->prevTitle = $row->prev->title;
				$row->prev = JRoute::_(ContentHelperRoute::getArticleRoute($row->prev->slug, $row->prev->catslug));
			} else {
				$row->prev = '';
				$row->prevTitle = '';
			}

			if ($row->next) {
				$row->nextTitle = $row->next->title;
				$row->next = JRoute::_(ContentHelperRoute::getArticleRoute($row->next->slug, $row->next->catslug));
			} else {
				$row->next = '';
				$row->nextTitle = '';
			}
			
			$prevnext_text = $this->params->get('prevnext_text', 0);
			$prev = JText::_('JPREV'); $next = JText::_('JNEXT');
			$prevnext_title = $this->params->get('prevnext_title', 1);
			$prevnext_standard = $this->params->get('prevnext_standard', 0);
			
			// Output.
			if ($row->prev || $row->next) {
				$html = '<!-- Custom Page Navigation | Powered by <a href="http://www.e-max.it" title="Web Marketing" target="_blank">Web Marketing</a> -->
				<ul class="pagenav">'
				;
				if ($row->prev) {
					if ($prevnext_text) {
					$html .= '
					<li class="pagenav-prev">
						<a href="'. $row->prev .'" rel="next" title="' .$row->prevTitle. '">'
							. JText::_('JGLOBAL_LT');
						if ($prevnext_title) {
							$html .= $pnSpace . $row->prevTitle;
						}
						if ($prevnext_standard) {
							$html .= $pnSpace . $prev;
						}
					$html .= '</a></li>';
					}
					else {
					$html .= '
					<li class="pagenav-prev">
						<a href="'. $row->prev .'" rel="next" title="' .$row->prevTitle. '"><img src="plugins/content/custompagenavigation/style/'.$this->params->get('arrow_sx').'" /><span>';
						if ($prevnext_title) {
							$html .= $row->prevTitle . $pnSpace;
						}
						if ($prevnext_standard) {
							$html .= $prev;
						}
					$html .= '</span></a></li>';
					}
				}

				

				if ($row->next) {
					if ($prevnext_text) {
					$html .= '
					<li class="pagenav-next">
						<a href="'. $row->next .'" rel="prev" title="' .$row->nextTitle. '">';
						if ($prevnext_standard) {
							$html .= $next . $pnSpace;
						}
						if ($prevnext_title) {
							$html .= $row->nextTitle . $pnSpace;
						}
					$html .= JText::_('JGLOBAL_GT') .'</a></li>';
					}
					else {
					$html .= '
					<li class="pagenav-next">
						<a href="'. $row->next .'" rel="prev" title="' .$row->nextTitle. '"><img src="plugins/content/custompagenavigation/style/'.$this->params->get('arrow_dx').'" /><span>';
						if ($prevnext_standard) {
							$html .= $next ;
						}
						if ($prevnext_title) {
							$html .= $pnSpace . $row->nextTitle;
						}
					$html .='</span></a></li>';
					}
				}
				$html .= '
				</ul>'
				;

			$credits = $this->params->get('credits');
			if ($credits) {
				$html .= '<div class="pagenavigation_credits">Powered by <a href="http://www.e-max.it" title="Web Marketing" target="_blank">Web Marketing</a></div>';
			}
			else {
				$html .= '<div class="pagenavigation_credits" style="display:none;">Powered by <a href="http://www.e-max.it" title="Web Marketing" target="_blank">Web Marketing</a></div>';
			}
			$html .= '<!-- Custom Page Navigation | Powered by <a href="http://www.e-max.it" title="Web Marketing" target="_blank">Web Marketing</a> -->';
			$row->text = str_replace('{loadnavigation}', $html, $row->text);
			}
		}

		return ;
	}
}