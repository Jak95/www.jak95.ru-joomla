<?php
/**
 * @copyright	Copyright (C) 2010 Michael Richey. All rights reserved.
 * @license		GNU General Public License version 3; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldJavascript extends JFormField
{
	protected $type = 'Javascript';

	protected function getInput()
	{
		$doc = &JFactory::getDocument();
                $declaration=array();
                $declaration[]="window.addEvent('domready', function() {";
                $declaration[]="\tdocument.formvalidator.setHandler('notnumeric',function (value) {";
                $declaration[]="\t\tregex=/^[0-9]+$/;";
                $declaration[]="\t\tif(regex.test(value) == true) {;";
                $declaration[]="\t\t\talert('".JText::_('PLG_SYS_ADMINEXILE_NOTNUMERIC')."');";
                $declaration[]="\t\t\treturn false;";
                $declaration[]="\t\t} else {";
                $declaration[]="\t\t\treturn true;";
                $declaration[]="\t\t};";
                $declaration[]="\t});";
                $declaration[]="});";
		$doc->addScriptDeclaration(implode("\n",$declaration));
		return;
	}
}
