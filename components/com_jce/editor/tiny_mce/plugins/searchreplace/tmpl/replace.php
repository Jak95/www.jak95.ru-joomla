<?php
/**
 * @package   	JCE
 * @copyright 	Copyright © 2009-2011 Ryan Demmer. All rights reserved.
 * @license   	GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('WF_EDITOR') or die('RESTRICTED');
?>
<table border="0" cellspacing="0" cellpadding="2">
	<tr>
		<td>
			<label for="replace_panel_searchstring">{#searchreplace_dlg.findwhat}</label>
			<input type="text" id="replace_panel_searchstring" name="replace_panel_searchstring" style="width: 200px" />
		</td>
	</tr>
	<tr>
		<td>
			<label for="replace_panel_replacestring">{#searchreplace_dlg.replacewith}</label>
			<input type="text" id="replace_panel_replacestring" name="replace_panel_replacestring" style="width: 200px" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label>{#searchreplace_dlg.direction}</label>
			<input id="replace_panel_backwardsu" name="replace_panel_backwards" class="radio" type="radio" />
			<label for="replace_panel_backwardsu">{#searchreplace_dlg.up}</label>
			<input id="replace_panel_backwardsd" name="replace_panel_backwards" class="radio" type="radio" checked="checked" />
			<label for="replace_panel_backwardsd">{#searchreplace_dlg.down}</label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input id="replace_panel_casesensitivebox" name="replace_panel_casesensitivebox" class="checkbox" type="checkbox" />
			<label for="replace_panel_casesensitivebox">{#searchreplace_dlg.mcase}</label>
		</td>
	</tr>
</table>