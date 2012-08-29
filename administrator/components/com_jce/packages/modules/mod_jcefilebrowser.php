<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if (JComponentHelper::getComponent('com_jce', true)->enabled === false) {
	return;
}

jimport('joomla.application.component.model');

JModel::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jce' . DS . 'models');
$model = JModel::getInstance('model', 'WF');

// authorize
if (!$model->authorize('browser')) {
	return;
}

JHtml::_('behavior.modal');

require_once(JPATH_ADMINISTRATOR.'/components/com_jce/helpers/browser.php');

$language = JFactory::getLanguage();

$language->load('com_jce', JPATH_ADMINISTRATOR);

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_jce/media/css/module.css');

$module = JModuleHelper::getModule('mod_jcefilebrowser');

$width 	= 800;
$height = 600;
$filter = '';

if ($module) {
	$params = new JParameter($module->params);
	$width 	= $params->get('width', 800);
	$height = $params->get('height', 600); 
	$filter = $params->get('filter', '');
}

$float = $language->isRTL() ? 'right' : 'left';

?>
<div id="cpanel">
	<div class="icon-wrapper" style="float:<?php echo $float; ?>;">
		<div class="icon">
			<a class="modal" rel="{handler: 'iframe', size: {x: <?php echo $width;?>, y: <?php echo $height;?>}}" href="<?php echo WFBrowserHelper::getBrowserLink('', $filter); ?>">
				<span class="jce-file-browser"></span>
				<span><?php echo JText::_('WF_QUICKICON_BROWSER'); ?></span>
			</a>
		</div>
	</div>
</div>