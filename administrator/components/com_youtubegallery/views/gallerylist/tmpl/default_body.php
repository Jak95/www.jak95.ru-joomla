<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item):

        $link2edit='index.php?option=com_youtubegallery&view=galleryform&layout=edit&id='.$item->id;
        //$link2refresh='javascript: document.getElementById(\'task\').value=\'refresh\';document.getElementById(\'boxchecked\').value=\''.$item->id.'\';this.form.submit();';
        //<a href="<?php echo $link2refresh; >"><?php echo JText::_('COM_YOUTUBEGALLERY_REFRESH'); ></a>
        
        
        ?>
        

        <tr class="row<?php echo $i % 2; ?>">
                <td>
                        <?php echo $item->id; ?>
                </td>
                <td>
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                        <a href="<?php echo $link2edit; ?>"><?php echo $item->galleryname; ?></a>
                </td>
                
                
                <td>
                        <?php echo $item->categoryname; ?>
                </td>
                
                <td>
                        
                        
                        <span style="">
                                
                                <?php echo JText::sprintf(JText::_('COM_YOUTUBEGALLERY_LASTUPDATE'),$item->lastplaylistupdate,$item->updateperiod); ?>
                        </span>
                </td>
                
        </tr>
<?php endforeach; ?>
