<?php
/**
 * Mobile Joomla!
 * http://www.mobilejoomla.com
 *
 * @version		1.0.3
 * @license		GNU/GPL v2 - http://www.gnu.org/licenses/gpl-2.0.html
 * @copyright	(C) 2008-2012 Kuneri Ltd.
 * @date		April 2012
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');

class JHTMLMjconfig
{
	static function formName($param_name)
	{
		return 'mjconfig_'.str_replace('.', '-', $param_name);
	}

	static function booleanParam($param_name, $MobileJoomla_Settings)
	{
		$values = array(
			JHTML::_('select.option', '1', JText::_('COM_MJ__ON')),
			JHTML::_('select.option', '0', JText::_('COM_MJ__OFF'))
		);
		return JHTMLMjconfig::radioParam($param_name, $values, $MobileJoomla_Settings);
	}

	static function g_booleanParam($param_name, $MobileJoomla_Settings)
	{
		$values = array(
			JHTML::_('select.option', '1', JText::_('COM_MJ__ON')),
			JHTML::_('select.option', '0', JText::_('COM_MJ__OFF')),
			JHTML::_('select.option', '', JText::_('COM_MJ__GLOBAL'))
		);
		return JHTMLMjconfig::radioParam($param_name, $values, $MobileJoomla_Settings);
	}

	static function listParam($param_name, $values, $MobileJoomla_Settings)
	{
		$name = JHTMLMjconfig::formName($param_name);
		return JHTML::_('select.genericlist', $values, $name, 'class="inputbox" size="1"', 'value', 'text', $MobileJoomla_Settings[$param_name]);
	}

	static function g_listParam($param_name, $values, $MobileJoomla_Settings)
	{
		array_unshift($values, JHTML::_('select.option', '', JText::_('COM_MJ__GLOBAL')));
		return JHTMLMjconfig::listParam($param_name, $values, $MobileJoomla_Settings);
	}

	static function radioParam($param_name, $values, $MobileJoomla_Settings)
	{
		$name = JHTMLMjconfig::formName($param_name);
		return JHTML::_('select.radiolist', $values, $name, 'class="inputradio"', 'value', 'text', $MobileJoomla_Settings[$param_name]);
	}

	static function g_radioParam($param_name, $values, $MobileJoomla_Settings)
	{
		array_push($values, JHTML::_('select.option', '', JText::_('COM_MJ__GLOBAL')));
		return JHTMLMjconfig::radioParam($param_name, $values, $MobileJoomla_Settings);
	}

	static function templateParam($param_name, $templates, $MobileJoomla_Settings)
	{
		$name = JHTMLMjconfig::formName($param_name);
		return JHTML::_('mjconfig.selectarray', $templates, $name, 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings[$param_name]);
	}

	static function positionParam($param_name, $positions, $MobileJoomla_Settings)
	{
		$name = JHTMLMjconfig::formName($param_name);
		return JHTML::_('select.genericlist', $positions, $name, 'class="inputbox" size="1"', 'value', 'value', $MobileJoomla_Settings[$param_name]);
	}

	static function label($label, $tooltip = '', $for_input = '')
	{
		$label = htmlspecialchars(JText::_($label), ENT_QUOTES, 'UTF-8');
		if($tooltip)
		{
			$tooltip = 'class="hasTip" title="'.addslashes($label).'::'.addslashes(htmlspecialchars(JText::_($tooltip), ENT_QUOTES, 'UTF-8')).'"';
		}
		if($for_input)
		{
			$name = JHTMLMjconfig::formName($for_input);
			$for_input = 'for="'.$name.'"';
		}
		return "<label $for_input $tooltip>$label</label>";
	}

	static function textinput($param_name, $value, $size = 0, $attrs = NULL)
	{
		$name = JHTMLMjconfig::formName($param_name);
		$value = addslashes(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
		if(!$attrs)
		{
			$attrs = array();
		}
		if(!isset($attrs['id']) || $attrs['id']=='')
		{
			$attrs['id'] = $name;
		}
		$attrs['name'] = $name;
		$attrs['value'] = $value;
		if($size)
		{
			$attrs['size'] = $size;
		}
		else
		{
			$attrs['class'] = isset($attrs['class']) ? $attrs['class'].' fullwidth' : 'fullwidth';
		}
		$attr_list = array();
		foreach($attrs as $attr=>$val)
		{
			$attr_list[] = "$attr=\"$val\"";
		}
		$attr_str = join(' ',$attr_list);
		return "<input $attr_str>";
	}

	static function selectArray(&$arr, $tag_name, $tag_attribs, $key, $text, $selected = NULL)
	{
		reset($arr);
		$html = "<select name=\"$tag_name\" $tag_attribs>";
		$count = count($arr);
		for($i = 0; $i < $count; $i++)
		{
			$k = $arr[$i][$key];
			$extra = ($k == $selected ? " selected=\"selected\"" : '');
			$html .= "<option value=\"".$k."\"$extra>".$arr[$i][$text]."</option>";
		}
		$html .= "</select>";
		return $html;
	}

	static function menuList($menuoptions, $param_name, $value)
	{
		$name = JHTMLMjconfig::formName($param_name);

		static $is_joomla15;
		if(!isset($is_joomla15))
			$is_joomla15 = (substr(JVERSION,0,3) == '1.5');

		if(!$is_joomla15)
			return JHTML::_('select.genericlist',
							$menuoptions,
							$name.'_tmp',
							array('list.attr' => 'size="7" onchange="document.getElementById(\''.$name.'\').value=this.value" ',
								  'list.select' => $value,
								  'option.text.toHtml' => false));
		else
			return JHTML::_('select.genericlist',
							$menuoptions,
							$name.'_tmp',
							'size="7" onchange="document.getElementById(\''.$name.'\').value=this.value" ',
							'value',
							'text',
							$value);
	}
}
