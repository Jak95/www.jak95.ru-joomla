<?php
/*
 * v1.0.0
 * Friday Sep-02-2011
 * @component JooComments
 * @copyright Copyright (C) Abhiram Mishra www.bullraider.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

$i=0;?>
<h2 class="comment-header"><?php echo count($this->comments).' '.JText::sprintf('COM_JOOCOOMENTS_COMMENTS_LIST_COMMENT_NUMBER');?> </h2><hr/>
<div>
<?php foreach($this->comments as  $item):  ?>
<div class="baksha">
<div class="comment normal-comment">
<div class="cmeta">
<p class="author"><strong class="author"><?php echo $item['name']; ?></strong>
<em class="action-text"><?php  echo JText::sprintf('COM_JOOCOOMENTS_COMMENTS_LIST_USER_SAID');?> </em></p>
<?php 
if (intval($item['publish_date'] ) != 0){?>
	<p class="info">
      <em class="date">
      	<?php  echo substr($item['publish_date'], 0, 10); ?>
      </em>
    </p>
<?php }?>
</div>
<div class="body" id="commentid<?php echo $i; $i++;?>"><?php echo Markdown($item['comment']); ?></div>
</div>
</div>
<div style="padding-top:12px;" ></div>

<?php endforeach; ?>
</DIV>
<script type="text/javascript" language="javascript">
var length=<?php echo count($this->comments)?>;
</script>
<?php die();?>