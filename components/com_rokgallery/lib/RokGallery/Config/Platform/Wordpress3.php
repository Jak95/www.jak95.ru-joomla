<?php
/**
  * @version   $Id: Joomla15.php 39200 2011-06-30 04:31:21Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */


/**
 *
 */
class RokGallery_Config_Platform_Wordpress3 implements RokGallery_Config_Platform
{

    /**
     *
     */
    public function __construct()
    {
        $this->options =& get_option('rokgallery_admin_options');
    }

    /**
     * @param $name
     * @param null $default
     * @param null $context
     * @return mixed the options value
     */
    public function getOption($name, $default = null, $context = null)
    {
        $value = $default;
        switch($name){
            case RokGallery_Config::OPTION_THUMBNAIL_BASE_URL:
            case RokGallery_Config::OPTION_BASE_URL:
                //TODO: Change this to return from a router
                $value = WP_CONTENT_URL .'/uploads/rokgallery/';
                break;
            case RokGallery_Config::OPTION_ROOT_PATH:
                $value = WP_CONTENT_DIR .'/uploads/rokgallery/';
                break;
            case RokGallery_Config::OPTION_JOB_QUEUE_PATH;
                $value = WP_CONTENT_DIR. '/uploads/';
                break;
            default:
                $value = $this->options->get($name, $default);
                break;
        }
        return $value;
    }
}
