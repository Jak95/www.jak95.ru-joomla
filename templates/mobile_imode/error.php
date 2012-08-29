<?php
/**
 * Mobile Joomla!
 * http://www.mobilejoomla.com
 *
 * @version		1.1.0
 * @license		GNU/GPL v2 - http://www.gnu.org/licenses/gpl-2.0.html
 * @copyright	(C) 2008-2012 Kuneri Ltd.
 * @date		June 2012
 */
defined('_JEXEC') or die('Restricted access');

defined('_MJ') or die('Incorrect usage of Mobile Joomla.');

$MobileJoomla = MobileJoomla::getInstance();

$MobileJoomla->showDocType();
?>
<html>
<head>
<meta name="CHTML">
<meta http-equiv="Content-Type" content="<?php echo $MobileJoomla->getContentString(); ?>">
<title><?php echo $this->title; ?></title>
</head>
<body>
<div><b><?php echo $this->error->get('code'); ?> - <?php echo $this->error->get('message'); ?></b></div>
<?php if($this->debug) echo '<div>'.$this->renderBacktrace().'</div>'; ?>
</body>
</html>