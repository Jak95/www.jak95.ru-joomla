<?php
/*
 * v1.0.0
 * Friday Sep-02-2011
 * @component JooComments
 * @copyright Copyright (C) Abhiram Mishra www.bullraider.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width="5">
		<?php echo JText::_('id'); ?>
	</th>
	<th width="20">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>			
	<th>
		<?php echo JText::sprintf('COM_JOOCOMMENTS_TABLE_HEADING_USER_DETAILS'); ?>
	</th>
	<th>
		<?php echo JText::sprintf('COM_JOOCOMMENTS_TABLE_HEADING_COMMENT_DETAILS'); ?>
	</th>
	<th>
		<?php echo JText::sprintf('COM_JOOCOMMENTS_TABLE_HEADING_IS_PUBLISHED'); ?>
	</th>
</tr>
