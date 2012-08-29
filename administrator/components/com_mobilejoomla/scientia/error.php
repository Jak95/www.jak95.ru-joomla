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
global $mootools;

?><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link href="style.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo $mootools; ?>" type="text/javascript"></script>
	<script src="main.js" type="text/javascript"></script>
</head>
<body>

<h1>Problem with remote connection</h1>
<p>Your server does not support remote connections. Please download Scientia Mobile DB-API Plugin manually, install as any other Joomla! plugin.</p>
<div>
	<div class="floatleft"><a href="http://www.mobilejoomla.com/" target="_blank" class="button enabled">Download</a></div>
	<div class="floatleft"><a href="index.php?action=install" class="button enabled">Try again</a></div>
</div>
<div class="clear"></div>

</body>
</html>