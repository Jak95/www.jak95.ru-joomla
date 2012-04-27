<?php
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<td colspan="5">
		<?php echo $this->pagination->getListFooter(); ?>
		<br/><br/>
		<div align="center" style="clear: both">
			<?php echo sprintf(JText::_('COM_GCALENDAR_FOOTER'), JRequest::getVar('GCALENDAR_VERSION'));?>
		</div>
	</td>
</tr>