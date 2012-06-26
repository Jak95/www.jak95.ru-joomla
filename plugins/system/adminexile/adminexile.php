<?php

/**
* @package plugin AdminExile
* @copyright (C) 2010-2011 RicheyWeb - www.richeyweb.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* AdminExile Copyright (c) 2011 Michael Richey.
* AdminExile is licensed under the http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*
* AdminExile version 1.2 for Joomla 1.6.x devloped by RicheyWeb
*
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * AdminExile system plugin
 */
class plgSystemAdminExile extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemAdminExile( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}

	/* The generator tag isn't added until the document is rendered */
	function onAfterInitialise()
	{
		$app = JFactory::getApplication();
		// this plugin is meant for administrator
		if($app->isAdmin()) {           
                    // Once you're in - you're in
                    if(JFactory::getUser()->id) return true;
                    
                    // the admin access key is required to exist in the session variables or in the URL
                    $key = $this->params->get('key','adminexile');                    

                    // first we check the session variable
                    if($app->getUserState("plg_sys_adminexile.$key",false)) {
                            return true;
                    } else {
                            // no session set, check the URL
                            if(array_key_exists($key,JRequest::get('GET'))) {
                                    if($this->params->get('twofactor',false) && ($this->params->get('keyvalue',false) != JRequest::getVar($key))) {
                                        $this->_redirect();
                                    } else {
                                        // found it, set the session and clear the key from the URL
                                        $app->setUserState("plg_sys_adminexile.$key",true);
                                        header("Location: ".JURI::root()."administrator");
                                        return true;
                                    }
                            } else {
                                    // no session, no url - redirecting
                                $this->_redirect();
                            }
                    }
                } else {
                    if(
                            !$this->params->get('frontrestrict',0) || (
                                    JRequest::getCmd('option','') != 'com_users' &&
                                    JRequest::getVar('task','') != 'user.login'
                            )
                    ) return true;
                    $db = JFactory::getDbo();
                    $query=$db->getQuery(true);
                    $query->select('id')->from('#__users')->where('username='.$db->quote(JRequest::getVar('username','','POST')));
                    $db->setQuery($query);
                    $user = JFactory::getUser($db->loadResult());
                    $restrictgroup = $this->params->get('restrictgroup',array());
                    foreach($restrictgroup as $group) {
                        if(in_array($group,$user->groups)) {
                            // this will give a nice non-descript error
                            JRequest::setVar('password','');
                            return true;
                        }
                    }
                }
	}
        public function _redirect() {
            $redirecturl = $this->params->get('redirect',JURI::root());
            switch($redirecturl) {
                case '{HOME}':
                    $redirecturl = JURI::root();
                    break;
                case '{404}':
                    header($_SERVER["SERVER_PROTOCOL"].' 404 Not Found');
                    header("Status: 404 Not Found");
                    if(!$this->params->get('fourofour',false)) {
                        die($_SERVER["SERVER_PROTOCOL"].' 404 Not Found');
                    } else {
                        $find=array('{url}','{serversignature}');
                        $replace=array($_SERVER['REQUEST_URI'],$_SERVER["SERVER_SIGNATURE"]);
                        die(str_replace($find,$replace,$this->params->get('fourofour')));
                    }
                    break;
                default:
                    break;
            }
            if($redirecturl != '{404}') header("Location: ".$redirecturl);
            return true;
        }
}
