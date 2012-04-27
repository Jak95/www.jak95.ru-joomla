<?php
/*------------------------------------------------------------------------
# redimhelp.php for PLG - SYSTEM - IMAGESIZER
# ------------------------------------------------------------------------
# author    reDim - Norbert Bayer
# copyright (C) 2011 redim.de. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.redim.de
# Technical Support:  Forum - http://www.redim.de/kontakt/
-------------------------------------------------------------------------*/

# reDim - Help V1.0
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldredimhelp extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	public $type  = 'redimhelp';
	public $_version = '1.6';


	protected function getInput()
	{
	#	$view =  $node->attributes('view');
		$view =  $this->element['view'];

	$document   =& JFactory::getDocument();
	$document->addScript(JURI::ROOT().'plugins/system/imagesizer/js/helper.js');

	$user = JFactory::getUser();
	JHtml::_('behavior.keepalive');

$html='<br style="clear: both;" />
<div id="'.$this->id.'" style="width: 300px;height: 300px; "><br />
<b>'.JText::_("IMAGESIZER_HELP_EMAIL").'</b>
<input name="helpemail" id="'.$this->id.'_helpemail" type="text" class="inputbox" value="'.$user->get("email").'" size="70" /><br /><br />
<b>'.JText::_("IMAGESIZER_HELP_MESSAGE").'</b>
<textarea class="inputbox" name="helptext" id="'.$this->id.'_helptext" cols="50" rows="8"></textarea><br />
<input name="helpkey" type="hidden" value="" /><br style="clear:both" />
<input type="button" style="cursor: pointer;" onclick="redim_helper_send(\''.$this->id.'\',$(\''.$this->id.'_helpemail\').value,$(\''.$this->id.'_helptext\').value);" value="'.JText::_("IMAGESIZER_SEND").'" />
</div>';

		return $html;

	}
}