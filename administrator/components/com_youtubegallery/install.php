<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * Script file of Youtube Gallery component
 */
class com_YoutubeGalleryInstallerScript
{
        /**
         * method to install the component
         *
         * @return void
         */
        function install($parent) 
        {

            $manifest = $parent->get("manifest");
            $parent = $parent->getParent();
            $source = $parent->getPath("source");
             
            $installer = new JInstaller();
            
            // Install plugins
            foreach($manifest->plugins->plugin as $plugin) {
                $attributes = $plugin->attributes();
                $plg = $source . DS . $attributes['folder'].DS.$attributes['plugin'];
                $installer->install($plg);
            }
            
            // Install modules
            foreach($manifest->modules->module as $module) {
                $attributes = $module->attributes();
                $mod = $source . DS . $attributes['folder'].DS.$attributes['module'];
                $installer->install($mod);
            }
            
            $db = JFactory::getDbo();
            $tableExtensions = $db->nameQuote("#__extensions");
            $columnElement   = $db->nameQuote("element");
            $columnType      = $db->nameQuote("type");
            $columnEnabled   = $db->nameQuote("enabled");
            
            // Enable plugins
            $db->setQuery(
                "UPDATE 
                    $tableExtensions
                SET
                    $columnEnabled=1
                WHERE
                    $columnElement='youtubegallery'
                AND
                    $columnType='plugin'"
            );
            
            $db->query();
            
			echo '<p>' . JText::_('COM_YOUTUBEGALLERY_INSTALL_TEXT') . '</p>';
			
        }
 
        /**
         * method to uninstall the component
         *
         * @return void
         */
        function uninstall($parent) 
        {
                // $parent is the class calling this method
				$db = JFactory::getDbo();
				
				
		        $db->setQuery('SELECT extension_id FROM #__extensions WHERE type="plugin" AND element = "youtubegallery" AND folder = "content"  LIMIT 1');
		        $result = $db->loadResult();

		        if ($result)
				{
	                $installer = new JInstaller(); 
	                $installer->uninstall('plugin', $result);
			        
				}
            
	            // Uninstall module
		        
		        $db->setQuery('SELECT extension_id FROM #__extensions WHERE type="module" AND element = "mod_youtubegallery" LIMIT 1');
		        $result = $db->loadResult();
		        if ($result)
				{

	                $installer = new JInstaller(); 
	                $installer->uninstall('module', $result);
			        
				}

			

				
                echo '<p>' . JText::_('COM_YOUTUBEGALLERY_UNINSTALL_TEXT') . '</p>';
        }
 
        /**
         * method to update the component
         *
         * @return void
         */
        function update($parent) 
        {
                // $parent is the class calling this method
				
				$manifest = $parent->get("manifest");
				$parent = $parent->getParent();
				$source = $parent->getPath("source");
             
	            $installer = new JInstaller();
            
	            // Install plugins
	            foreach($manifest->plugins->plugin as $plugin) {
	                $attributes = $plugin->attributes();
	                $plg = $source . DS . $attributes['folder'].DS.$attributes['plugin'];
	                $installer->install($plg);
	            }
            
	            // Install modules
	            foreach($manifest->modules->module as $module) {
	                $attributes = $module->attributes();
	                $mod = $source . DS . $attributes['folder'].DS.$attributes['module'];
	                $installer->install($mod);
	            }
				
                echo '<p>' . JText::_('COM_YOUTUBEGALLERY_UPDATE_TEXT') . '</p>';
        }
 
        /**
         * method to run before an install/update/uninstall method
         *
         * @return void
         */
        function preflight($type, $parent) 
        {
                // $parent is the class calling this method
                // $type is the type of change (install, update or discover_install)
                //echo '<p>' . JText::_('COM_YOUTUBEGALLERY_PREFLIGHT_' . $type . '_TEXT') . '</p>';
        }
 
        /**
         * method to run after an install/update/uninstall method
         *
         * @return void
         */
        function postflight($type, $parent) 
        {
                // $parent is the class calling this method
                // $type is the type of change (install, update or discover_install)
                //echo '<p>' . JText::_('COM_YOUTUBEGALLERY_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
        }
}
