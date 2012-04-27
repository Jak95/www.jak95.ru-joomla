<?php
/**
  * @version   $Id:$
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

defined('_JEXEC') or die('Restricted index access');
jimport('joomla.plugin.plugin');
jimport('joomla.utilities.utility');

class plgSystemRokGallery extends JPlugin {

	function plgSystemRokGallery(&$subject, $config){
		parent::__construct($subject, $config);
	}

	
	function onBeforeCompileHead()
    {
        $version = new JVersion();

        if ($version->getShortVersion() < '1.5.23')
            return;

        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');
        $app =& JFactory::getApplication();

        if ($option == 'com_rokgallery' && $view == 'gallerypicker' && $app->isSite()){
            $this->_cleanView();
		}
	}

    function _cleanView()
    {
        $path = (JFactory::getApplication()->isSite()) ? JPATH_COMPONENT_ADMINISTRATOR : JPATH_COMPONENT;
        require_once ($path.'/helpers/rokgallery.php');

        RokGalleryHelper::setupGalleryPicker();

		return true;
    }
}