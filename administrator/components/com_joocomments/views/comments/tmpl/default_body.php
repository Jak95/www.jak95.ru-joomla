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

 foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>
			<strong><?php echo JText::sprintf('COM_JOOCOMMENTS_TABLE_BODY_COMMENTATOR_NAME').': ';?></strong><?php echo $item->name; ?><br/><br/>
			<strong><?php echo JText::sprintf('COM_JOOCOMMENTS_TABLE_BODY_COMMENTATOR_EMAIL').': ';?> </strong><?php echo $item->email; ?>
			
		</td>
		<td>
			<strong><?php echo JText::sprintf('COM_JOOCOMMENTS_TABLE_BODY_COMMENTATOR_ARTICLE_TITLE').': ';?> </strong><?php echo $item->title; ?><br/><br/>
			<strong><?php echo JText::sprintf('COM_JOOCOMMENTS_TABLE_BODY_COMMENTATOR_COMMENT').': ';?> </strong><?php echo $item->comment; ?>
			
		</td>
		<td>
		<?php echo JHtml::_('jgrid.published', $item->published, $i, 'comments.'); ?>
		</td>
	</tr>
<?php endforeach; ?>
