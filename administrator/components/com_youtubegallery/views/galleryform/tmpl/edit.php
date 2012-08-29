<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
?>

<script language="javascript">
        function SwithTabs(nameprefix, count, activeindex)
        {
                for(i=0;i<count;i++)
                {
                        var obj=document.getElementById(nameprefix+i);
                        obj.style.display="none";
                }
                
                var obj=document.getElementById(nameprefix+activeindex);
                obj.style.display="block";
        }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_youtubegallery&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="youtubegallery-form" class="form-validate">
        <fieldset class="adminform">
                <?php echo $this->form->getInput('id'); ?>
                
                
                <legend><?php echo JText::_( 'COM_YOUTUBEGALLERY_FORM_DETAILS' ); ?> (Free Version)</legend>
                
                <p>
                <?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?>
                </p>
                <p>
                <?php echo $this->form->getLabel('galleryname'); ?>
				<?php echo $this->form->getInput('galleryname'); ?>
                </p>
                
                
                <br /><br />
                <?php //Links ?> <h4>List of Video Links</h4>
                <div style="border: 1px dotted #000000;padding:10px;margin:0px;">
                       
                        <table style="border:none;">
                                <tbody>
                                        <tr><td><?php echo $this->form->getLabel('gallerylist'); ?></td><td>:</td><td><?php echo $this->form->getInput('gallerylist'); ?></td>
										
										<td>
										
												<?php //-------------------------- ?>
										
										
										  <div style="border: 1px dotted #000000;padding:10px;margin:0px;">
                       
                        <table style="border:none;">
                                <tbody>
                                        <tr>	<td>
									
						example:<br>
						http://www.youtube.com/watch?v=baLkXC_qWJY&feature=related<br>
						http://www.youtube.com/watch?v=H1wXsygQTVA&feature=related<br>
						http://www.youtube.com/watch?v=VSGMqfGmjG0<br>
						<br>
						http://www.youtube.com/playlist?list=PL5298F5DAD70298FC&feature=mh_lolz<br>
						http://www.youtube.com/user/designcompasscorp<br>
						youtubestandard:<i>video_feed</i><br>
						
						<a href="http://extensions.designcompasscorp.com/index.php/youtube-gallery-standard-feeds" target="_blank">More about Standard Video Feeds</a><br>
						<br>
						http://vimeo.com/8761657<br>
						http://video.yahoo.com/watch/2342109/7336957<br>
						http://video.google.com/videoplay?docid=-1667589095394987118#<br>
						http://www.collegehumor.com/video/6446891/what-pi-sounds-like<br>
						
										
											<?php
						
						if(ini_get('allow_url_fopen')==0)
						{
							echo '<p>
						<b>Please note:</b><br>
						
						For Yahoo! Video, Vimeo and Google Video<br>
						<span style="color: red;">php 5.x and [allow_url_fopen=on] record in php.ini file are required.</span>
						</p>';
						}
						?>
						<p>
						Also you may have your own title, description and thumbnail for each video.<br>
						To do this type comma then "title","description","imageurl","special_parameters"<br>
						Should look like: http://www.youtube.com/watch?v=baLkXC_qWJY,"Video Title","Video description","images/customthumbnail.jpg"<br>
						or<br>
						http://www.youtube.com/watch?v=baLkXC_qWJY,"Video Title",,"images/customthumbnail.jpg"
						</p>
						<p>Special parameter:
						<br><br>max-results=<i>NUMBER</i>,start-index=<i>NUMBER</i>,orderby=<i>FIELD_NAME</i>
						<br><a href="http://extensions.designcompasscorp.com/index.php/youtube-gallery-special-parameters" target="_blank">More about Special Parameters</a>
						</p>
										

										</td></tr>
										
						
						
                                </tbody>
                        </table>
                </div>
                <?php //-------------------------- ?>
				</td>
										
										</tr>
                                </tbody>
                        </table>
                </div>
                
                <p><br /><br /></p>
               
                <?php $d=($this->form->getvalue('customlayout')!='' ? 'none' : 'block' ); ?>
                <div style="border: 1px dotted #000000;padding:10px;margin:0px;display: <?php echo $d; ?>;" id="layouttab_0" class="layouttab_content">
                        <div style="margin-top:-50px;">
                                 <?php //Layout Wizard ?> <h4>Layout Wizard | <a href="javascript: SwithTabs('layouttab_',2,1)">Custom Layout</a></h4>
                        </div>
                        <table style="border:none;">
                                <tbody>
                                        <tr><td><?php echo $this->form->getLabel('showgalleryname'); ?></td><td>:</td><td><?php echo $this->form->getInput('showgalleryname'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('gallerynamestyle'); ?></td><td>:</td><td>AVAILABLE IN "PRO" VERSION ONLY </td></tr>
                                        <tr><td><?php echo $this->form->getLabel('pagination'); ?></td><td>:</td><td><?php echo $this->form->getInput('pagination'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('showactivevideotitle'); ?></td><td>:</td><td><?php echo $this->form->getInput('showactivevideotitle'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('playvideo'); ?></td><td>:</td><td><?php echo $this->form->getInput('playvideo'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('width'); ?></td><td>:</td><td><?php echo $this->form->getInput('width'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('height'); ?></td><td>:</td><td><?php echo $this->form->getInput('height'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('descr_style'); ?></td><td>:</td><td>AVAILABLE IN "PRO" VERSION ONLY </td></tr>
                                        <tr><td><?php echo $this->form->getLabel('descr_position'); ?></td><td>:</td><td><?php echo $this->form->getInput('descr_position'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('description'); ?></td><td>:</td><td><?php echo $this->form->getInput('description'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('cssstyle'); ?></td><td>:</td><td>AVAILABLE IN "PRO" VERSION ONLY </td></tr>
                                </tbody>
                        </table>
                </div>
                
                <?php $d=($this->form->getvalue('customlayout')!='' ? 'block' : 'none' ); ?>
                <div style="border: 1px dotted #000000;padding:10px;margin:0px;display: <?php echo $d; ?>;" id="layouttab_1" class="layouttab_content">
                        <div style="margin-top:-50px;">
                                 <?php //Layout Wizard ?> <h4><a href="javascript: SwithTabs('layouttab_',2,0)">Layout Wizard</a> | Custom Layout</h4>
                        </div>
                        <table style="border:none;">
                                <tbody>
                                        <tr>
                                                <td valign="top"><?php echo $this->form->getLabel('customlayout'); ?></td><td>:</td><td><?php echo $this->form->getInput('customlayout'); ?></td>
                                                <td valign="top">
                                                        
                                                        Layout tags:
                                                        
                                                        <table>
                                                                <tbody>
                                                                        <tr><td valign="top">[galleryname]</td><td>:</td><td>Gallery Name</td></tr>
                                                                        <tr><td valign="top">[videodescription]</td><td>:</td><td>Show Active Video Description</td></tr>
                                                                        <tr><td valign="top">[videoplayer]</td><td>:</td><td>Player</td></tr>
                                                                        <tr><td valign="top">[navigationbar]</td><td>:</td><td>Navigation Bar (list or table of thumbnails)</td></tr>
                                                                        <tr><td valign="top">[rel]</td><td>:</td><td>Rel option to apply any shadow/lightbox</td></tr>
                                                                        <tr><td valign="top">[count]</td><td>:</td><td>Number of videos (thumbnails)</td></tr>
                                                                        <tr><td valign="top">[pagination]</td><td>:</td><td>Pagination</td></tr>
                                                                        
                                                                </tbody>
                                                        </table>
                                                        
                                                        <br />
                                                        Example:
                                                        
                                                        
                                                        <textarea cols="30" rows="12"><h3>[galleryname]</h3>
[if:videodescription]<h4>[videodescription]</h4>[endif:videodescription]
[videoplayer]
[if:videotitle]<h3>[videotitle]</h3>[endif:videotitle]

[if:count]
<hr  style="border-color:#E7E7E9;border-style:solid;border-width:1px;"  />
[navigationbar:classictable,simple]
[endif:count]</textarea>
                                                
                                                </td>                                
                                                      
                                        
                                        </tr>
                                                                                
                                </tbody>
                        </table>
                </div>
                
                

                
                <?php //Player Settings ?><h4>Player Settings</h4>
                <div style="border: 1px dotted #000000;padding:10px;margin:0px;">
                        <table style="border:none;">
                                <tbody>
                                        <tr><td><?php echo $this->form->getLabel('border'); ?></td><td>:</td><td><?php echo $this->form->getInput('border'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('color1'); ?></td><td>:</td><td><?php echo $this->form->getInput('color1'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('color2'); ?></td><td>:</td><td><?php echo $this->form->getInput('color2'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('autoplay'); ?></td><td>:</td><td><?php echo $this->form->getInput('autoplay'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('repeat'); ?></td><td>:</td><td><?php echo $this->form->getInput('repeat'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('fullscreen'); ?></td><td>:</td><td><?php echo $this->form->getInput('fullscreen'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('related'); ?></td><td>:</td><td><?php echo $this->form->getInput('related'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('showinfo'); ?></td><td>:</td><td><?php echo $this->form->getInput('showinfo'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('controls'); ?></td><td>:</td><td><?php echo $this->form->getInput('controls'); ?></td></tr>
										<tr><td><?php echo $this->form->getLabel('muteonplay'); ?></td><td>:</td><td>AVAILABLE IN "PRO" VERSION ONLY</td></tr>
										<tr><td><?php echo $this->form->getLabel('volume'); ?></td><td>:</td><td><?php echo $this->form->getInput('volume'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('playertype'); ?></td><td>:</td><td><?php echo $this->form->getInput('playertype'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('youtubeparams'); ?></td><td>:</td><td><?php echo $this->form->getInput('youtubeparams'); ?></td></tr>
                                        
                                </tbody>
                        </table>
                </div>


                <?php //Navigation Bar ?><h4>Navigation Bar</h4>
                <div style="border: 1px dotted #000000;padding:10px;margin:0px;">
                        <table style="border:none;">
                                <tbody>
                                        <tr><td><?php echo $this->form->getLabel('navbarstyle'); ?></td><td>:</td><td><?php echo $this->form->getInput('navbarstyle'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('cols'); ?></td><td>:</td><td><?php echo $this->form->getInput('cols'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('bgcolor'); ?></td><td>:</td><td><?php echo $this->form->getInput('bgcolor'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('thumbnailstyle'); ?></td><td>:</td><td>AVAILABLE IN "PRO" VERSION ONLY </td></tr>
                                        <tr><td><?php echo $this->form->getLabel('showtitle'); ?></td><td>:</td><td><?php echo $this->form->getInput('showtitle'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('linestyle'); ?></td><td>:</td><td>AVAILABLE IN "PRO" VERSION ONLY </td></tr>
                                </tbody>
                        </table>
                </div>
		
			
                <?php //Misc ?><h4>Misc</h4>
                <div style="border: 1px dotted #000000;padding:10px;margin:0px;">
                        <table style="border:none;">
                                <tbody>
                                        <tr><td><?php echo $this->form->getLabel('customlimit'); ?></td><td>:</td><td><?php echo $this->form->getInput('customlimit'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('randomization'); ?></td><td>:</td><td>AVAILABLE IN "PRO" VERSION ONLY</td></tr>
                                        <tr><td><?php echo $this->form->getLabel('openinnewwindow'); ?></td><td>:</td><td><?php echo $this->form->getInput('openinnewwindow'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('rel'); ?></td><td>:</td><td><?php echo $this->form->getInput('rel'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('hrefaddon'); ?></td><td>:</td><td><?php echo $this->form->getInput('hrefaddon'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('useglass'); ?></td><td>:</td><td>AVAILABLE IN "PRO" VERSION ONLY</td></tr>
                                        <tr><td><?php echo $this->form->getLabel('logocover'); ?></td><td>:</td><td>AVAILABLE IN "PRO" VERSION ONLY</td></tr>
                                     <tr><td><?php echo $this->form->getLabel('updateperiod'); ?></td><td>:</td><td><?php echo $this->form->getInput('updateperiod'); ?></td></tr>
                                        <tr><td><?php echo $this->form->getLabel('prepareheadtags'); ?></td><td>:</td><td><?php echo $this->form->getInput('prepareheadtags'); ?></td></tr>

                                </tbody>
                        </table>
                </div>

        </fieldset>
        <div>
                <input type="hidden" name="task" value="galleryform.edit" />
                <?php echo JHtml::_('form.token'); ?>
        </div>
</form>