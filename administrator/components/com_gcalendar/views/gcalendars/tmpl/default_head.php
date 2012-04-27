<?php
defined('_JEXEC') or die('Restricted Access');
?><tr>
	<th width="5"><?php echo JText::_( 'COM_GCALENDAR_FIELD_NAME_ID_LABEL' ); ?></th>
	<th width="20"><input type="checkbox" name="toggle" value=""
		onclick="checkAll(<?php echo count( $this->items ); ?>);" /></th>
	<th><?php echo JText::_( 'COM_GCALENDAR_FIELD_NAME_LABEL' ); ?></th>
	<th><?php echo JText::_( 'COM_GCALENDAR_FIELD_COLOR_LABEL' ); ?></th>
	<th align="left"><?php echo JText::_( 'COM_GCALENDAR_VIEW_GCALENDARS_COLUMN_DETAILS' ); ?></th>
</tr>