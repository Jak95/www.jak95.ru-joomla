<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgXMLRPCIjoomla extends JPlugin
{
	function plgXMLRPCIjoomla(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage( '', JPATH_ADMINISTRATOR );
	}

	/**
	* @return array An array of associative arrays defining the available methods
	*/
	function onGetWebServices()
	{
		global $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		return array
		(
		'ijoomla.getCategories' => array(
		'function' => 'plgXMLRPCIjoomlaServices::getCategories',
		'docstring' => JText::_('Returns the list of Categories.'),
		'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString ))
		),
		
		'ijoomla.getCategoryBySection' => array(
		'function' => 'plgXMLRPCIjoomlaServices::getCategoryBySection',
		'docstring' => JText::_('Returns the list of Categories By a Section.'),
		'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString))
		),
		
		'ijoomla.getPosts' => array(
		'function' => 'plgXMLRPCIjoomlaServices::getPosts',
		'docstring' => JText::_('Returns the list of Posts.'),
		'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString))
		),

		'ijoomla.getSections' => array(
		'function' => 'plgXMLRPCIjoomlaServices::getSections',
		'docstring' => JText::_('Returns the list of Sections.'),
		'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString))
		),

		'ijoomla.getUsers' => array(
        'function' => 'plgXMLRPCIjoomlaServices::getUsers',
        'docstring' => JText::_('Returns the list of Users.'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString))
        ),


		
		'ijoomla.getCategory' => array(
		'function' => 'plgXMLRPCIjoomlaServices::getCategory',
		'docstring' => JText::_('Content of the Category.'),
		'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString))
		),
		
		'ijoomla.getPost' => array(
		'function' => 'plgXMLRPCIjoomlaServices::getPost',
		'docstring' => JText::_('Returns information about a specific post.'),
		'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString))
		),
		
		'ijoomla.getSection' => array(
		'function' => 'plgXMLRPCIjoomlaServices::getSection',
		'docstring' => 'Content of the Section',
		'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString))
		),
        
        'ijoomla.getUser' => array(
        'function' => 'plgXMLRPCIjoomlaServices::getUser',
        'docstring' => JText::_('Details of the User.'),
        'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString))
        ),
 

 
        'ijoomla.deleteCategory' => array(
        'function' => 'plgXMLRPCIjoomlaServices::deleteCategory',
        'docstring' => JText::_('Deletes a category.'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString))
        ),

		'ijoomla.deletePost' => array(
		'function' => 'plgXMLRPCIjoomlaServices::deletePost',
		'docstring' => JText::_('Move to trash a post.'),
		'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString))
		),

		'ijoomla.deletePostDefinitively' => array(
        'function' => 'plgXMLRPCIjoomlaServices::deletePostDefinitively',
        'docstring' => JText::_('Deletes a post.'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString))
        ),
		
		'ijoomla.deleteSection' => array(
        'function' => 'plgXMLRPCIjoomlaServices::deleteSection',
        'docstring' => JText::_('Delete a section.'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString))
        ),

		'ijoomla.deleteUser' => array(
        'function' => 'plgXMLRPCIjoomlaServices::deleteUser',
        'docstring' => JText::_('Deletes a user.'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString))
        ),

		
		
        'ijoomla.saveCategory' => array(
        'function' => 'plgXMLRPCIjoomlaServices::saveCategory',
        'docstring' => JText::_('Save Category'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString))
        ),
        
		'ijoomla.savePost' => array(
		'function' => 'plgXMLRPCIjoomlaServices::savePost',
		'docstring' => JText::_('Updates the information about an existing post or add one.'),
		'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString))
        ),

        'ijoomla.saveSection' => array(
        'function' => 'plgXMLRPCIjoomlaServices::saveSection',
        'docstring' => JText::_('Save Section'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString))
        ),

        'ijoomla.saveUser' => array(
        'function' => 'plgXMLRPCIjoomlaServices::saveUser',
        'docstring' => JText::_('Save User'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString))
        ),

        

		'ijoomla.resetPost' => array(
        'function' => 'plgXMLRPCIjoomlaServices::resetPost',
		'docstring' => JText::_('Deletes a post.'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString))
        ),
        
		'ijoomla.testConnection' => array(
        'function' => 'plgXMLRPCIjoomlaServices::testConnection',
        'docstring' => JText::_('Test Connection'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString))
        ),
        
		'ijoomla.getPostsHome' => array(
        'function' => 'plgXMLRPCIjoomlaServices::getPostsHome',
        'docstring' => JText::_('Returns the list of Posts published in home'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString))
        ),
         
        'ijoomla.sendMail' => array(
        'function' => 'plgXMLRPCIjoomlaServices::sendMail',
        'docstring' => JText::_('Send Mass Mail'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString))
        ),
         
        'ijoomla.getGroups' => array(
        'function' => 'plgXMLRPCIjoomlaServices::getGroups',
        'docstring' => JText::_('Returns the list of Groups'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString))
        ),
        
		'ijoomla.updateHome' => array(
        'function' => 'plgXMLRPCIjoomlaServices::updateHome',
        'docstring' => JText::_('Update the list of posts of the home'),
        'signature' => array(array($xmlrpcArray, $xmlrpcArray, $xmlrpcString, $xmlrpcString))
		),
					
		'ijoomla.uploadImage' => array(
        'function' => 'plgXMLRPCIjoomlaServices::uploadImage',
        'docstring' => JText::_('Update a foto'),
        'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcBase64))
        )
		);
	}
}

class plgXMLRPCIjoomlaServices
{
	//POST SECTION
    
    function getPosts($trash, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Login failed'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		$db =& JFactory::getDBO();
        $user =& JFactory::getUser($username);
        plgXMLRPCIjoomlaHelper::getUserAid($user);
		$iduser = $user->get('id');
		
        if ($trash == 0)
        {
            $query = 'SELECT c.id, c.title, u.name, c.checked_out, cc.title AS category_title FROM #__content AS c LEFT JOIN #__categories AS cc ON cc.id = c.catid LEFT JOIN #__sections AS s ON s.id = c.sectionid LEFT JOIN #__groups AS g ON g.id = c.access LEFT JOIN #__users AS u ON u.id = c.checked_out LEFT JOIN #__users AS v ON v.id = c.created_by LEFT JOIN #__content_frontpage AS f ON f.content_id = c.id WHERE c.state <> -2';
            
        }
        else {
			if ($user->get('gid') < 24) {
				$structArray = array();
				$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('You do not have administrative privileges.'))), 'struct');
				return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
			}
            $query = 'SELECT c.id, c.title, u.name, c.checked_out, cc.title AS category_title FROM #__content AS c LEFT JOIN #__categories AS cc ON cc.id = c.catid LEFT JOIN #__sections AS s ON s.id = c.sectionid LEFT JOIN #__groups AS g ON g.id = c.access LEFT JOIN #__users AS u ON u.id = c.checked_out LEFT JOIN #__users AS v ON v.id = c.created_by LEFT JOIN #__content_frontpage AS f ON f.content_id = c.id WHERE c.state = -2';
        }
        
		$db->setQuery($query);
		$items = $db->loadObjectList();
        
		if ($items === null) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('No Articles found'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
			
		foreach ($items as $item)
		{
			$structArray[] = new xmlrpcval(array(
                                                 'title'	=> new xmlrpcval($item->title),
                                                 'category'	=> new xmlrpcval($item->category_title),
												 'checked'	=> new xmlrpcval($item->checked_out),
												 'checkedby'	=> new xmlrpcval($item->name),
												 'selfid'	=> new xmlrpcval($iduser),
                                                 'postid'		=> new xmlrpcval($item->id)
                                                 ), 'struct');
		}
        
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    
    function getPost($postid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Login failed'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}

		$item =& JTable::getInstance('content');
		if(!$item->load($postid)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The article does not exist')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
        $user =& JFactory::getUser($username);
        plgXMLRPCIjoomlaHelper::getUserAid($user);
        if($item->isCheckedOut($user->get('id'))) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The article is already being edited'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
        $query = 'SELECT COUNT(*)'
		. ' FROM #__content_frontpage'
		. ' WHERE content_id = '.$postid;
        
        $db =& JFactory::getDBO();
        
		$db->setQuery( $query );
        $home = $db->loadResult();   
        
        $structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
		$structArray[] = new xmlrpcval(array(
                                             'id'	=> new xmlrpcval($item->id),
                                             'title'		=> new xmlrpcval($item->title),
                                             'introtext'		=> new xmlrpcval($item->introtext),
                                             'state'		=> new xmlrpcval($item->state),
                                             'sectionid'		=> new xmlrpcval($item->sectionid),
                                             'catid'		=> new xmlrpcval($item->catid),
                                             'created'			=> new xmlrpcval($item->created),
                                             'modified'			=> new xmlrpcval($item->modified),
                                             'hits'			=> new xmlrpcval($item->hits),
                                             'home'			=> new xmlrpcval($home),
                                             'access'			=> new xmlrpcval($item->access)
                                             ), 'struct');
        
        
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    function deletePost($postid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
		// load the row from the db table
		$item =& JTable::getInstance('content');
		if(!$item->load( $postid )) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('No Articles found.')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
		$user =& JFactory::getUser($username);
        plgXMLRPCIjoomlaHelper::getUserAid($user);
        if($item->isCheckedOut($user->get('id'))) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Article is already being edited')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		$item->state = -2;
		$item->ordering = 0;
        
		if (!$item->store()) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The article has not been moved to the trash')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}		
		
		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval('Ok')), 'struct');
        return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    
	function savePost($postid, $title, $text, $published, $home, $idcat, $idsec, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
            
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
            
        // load the row from the db table
		$item =& JTable::getInstance('content');
		$item->load( $postid );
        
        $user =& JFactory::getUser($username);
        plgXMLRPCIjoomlaHelper::getUserAid($user);
		if($item->isCheckedOut($user->get('id'))) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The Article is already being edited'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
                       
        $item->title	 = $title;
		$item->introtext = $text;
		$item->sectionid = $idsec;
		$item->catid	 = $idcat;
        $item->state	 = $published;
        
        if ($postid == 0){
            $date =& JFactory::getDate();
            
			$item->created		= $date->toMySQL();
			$item->created_by	= $user->get('id');
            
			$item->publish_up	= $date->toMySQL();
        }
        else {
            $date =& JFactory::getDate();
            
            $item->modified		= $date->toMySQL();
			$item->modified_by	= $user->get('id');
        }
            
        if (!$item->check()) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Article check failed. Controls fields included.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
        $item->version++;
            
        if (!$item->store()) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The article was not stored'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
        if ($home==1 && $postid!=0) {
            $query = 'SELECT COUNT(*)'
            . ' FROM #__content_frontpage'
            . ' WHERE content_id = '.$postid;
            
            $db =& JFactory::getDBO();
            
            $db->setQuery( $query );
            $count = $db->loadResult();
            if($count==0) {
                $query = 'INSERT INTO #__content_frontpage (content_id, ordering) VALUES ('.$postid.', 0)';
                $db->setQuery( $query );
                $db->query();
            }
        }
        else if ($home==1 && $postid==0){
            $query = 'SELECT MAX(id) FROM #__content';
            $db =& JFactory::getDBO();
            
            $db->setQuery( $query );
            $count = $db->loadResult();
            $query = 'INSERT INTO #__content_frontpage (content_id, ordering) VALUES ('.$count.', 0)';
            $db->setQuery( $query );
            $db->query();
        }
        else if ($home==0 && $postid!=0) {
            $query = 'SELECT COUNT(*)'
            . ' FROM #__content_frontpage'
            . ' WHERE content_id = '.$postid;
            
            $db =& JFactory::getDBO();
            
            $db->setQuery( $query );
            $count = $db->loadResult();
            if($count==1) {
                $query = 'DELETE FROM #__content_frontpage WHERE content_id = '.$postid;
                $db->setQuery( $query );
                $db->query();
            }
        }
            
        $structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
        return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
    }
    
    //END POST
  
    //CATEGORY   
	function getCategories($username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}

		$db =& JFactory::getDBO();

		$query = ''.
        'SELECT #__categories.id, #__categories.title, #__categories.section, #__categories.checked_out, #__sections.title title_section, #__categories.published
        FROM #__categories
        INNER JOIN #__sections ON #__sections.id = #__categories.section';

		$db->setQuery($query);
		$items = $db->loadObjectList();

		if ($items === null) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('No categories found.')), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
		
		$user =& JFactory::getUser($username);
        plgXMLRPCIjoomlaHelper::getUserAid($user);
		$iduser = $user->get('id');
        
        $structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');       
        foreach ($items as $item)
		{
			$structArray[] = new xmlrpcval(array(
                                                 'title'	=> new xmlrpcval($item->title),
                                                 'id'		=> new xmlrpcval($item->id),
												 'checked'		=> new xmlrpcval($item->checked_out),
												 'selfid'		=> new xmlrpcval($iduser),
                                                 'title_section'		=> new xmlrpcval($item->title_section)
                                                 ), 'struct');
		}
        
        
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    function getCategoryBySection($idsection, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		$db =& JFactory::getDBO();
        
		$query = 'SELECT title,'
		. ' id'
		. ' FROM #__categories'
		. ' WHERE published = 1 AND section = '.$idsection.'';
        
		$db->setQuery($query);
		$items = $db->loadObjectList();
        
		if ($items === null) {
			return new xmlrpcresp(0, $xmlrpcerruser+1, JText::_('No categories found.') );
		}
        
		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
		foreach ($items as $item)
		{
			$structArray[] = new xmlrpcval(array(
                                                 'title'	=> new xmlrpcval($item->title),
                                                 'id'		=> new xmlrpcval($item->id)
                                                 ), 'struct');
		}
        
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    
    function getCategory($idcategory, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));	
		}
        
        // load the row from the db table
		$item =& JTable::getInstance('category');
		if(!$item->load($idcategory)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Category does not exist.')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
        $user =& JFactory::getUser($username);
        plgXMLRPCIjoomlaHelper::getUserAid($user);
        if($item->isCheckedOut($user->get('id'))) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The Category is already being edited'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
		$db =& JFactory::getDBO();
        
        $query = 'SELECT COUNT(*) FROM jos_content WHERE catid = '.$idcategory.' AND state <> -2';
        $db->setQuery($query);
        $active = $db->loadResult();
        
        $query = 'SELECT COUNT(*) FROM jos_content WHERE catid = '.$idcategory.' AND state = -2';
        $db->setQuery($query);
        $inactive = $db->loadResult();
        
		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
		$structArray[] = new xmlrpcval(array(
                                            'id'	=> new xmlrpcval($item->id),
                                            'section'		=> new xmlrpcval($item->section),
                                            'title'	=> new xmlrpcval($item->title),
                                            'published'		=> new xmlrpcval($item->published),
                                            'active'		=> new xmlrpcval($active),
                                            'inactive'		=> new xmlrpcval($inactive),
                                            'access'		=> new xmlrpcval($item->access),
                                            'description'	=> new xmlrpcval($item->description)
                                            ), 'struct');        
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    function deleteCategory($catid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
		// load the row from the db table
		$item =& JTable::getInstance('category');
		if(!$item->load( $catid )) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Category does not exist')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
        $user =& JFactory::getUser($username);
		plgXMLRPCIjoomlaHelper::getUserAid( $user );
		if($item->isCheckedOut($user->get('id'))) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Category is already being edited')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
        $query = 'SELECT COUNT(*)'
		. ' FROM #__content'
		. ' WHERE catid = '.$catid;
        
        $db =& JFactory::getDBO();
        
		$db->setQuery( $query );
        $count = $db->loadResult();
        
        if ($count != 0) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('This Category has at least one Article. You can not complete the operation.')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		if (!$item->delete()) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Category has not been deleted')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}		
		
		$structArray = array();
		$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval('Ok')), 'struct');
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    function saveCategory($idcat, $title, $text, $published, $idsec, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
            
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
            
        // load the row from the db table
        $item =& JTable::getInstance('category');
        if(!$item->load( $idcat ) && $idcat!=0) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The Category does not exist'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
            
        $user =& JFactory::getUser($username);
        plgXMLRPCIjoomlaHelper::getUserAid( $user );
        if($item->isCheckedOut($user->get('id'))) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The Category is already being edited'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
            
        $item->title	 = $title;
        $item->description = $text;
        $item->section = $idsec;
        $item->published		= $published;
        
        if ($idcat==0) {
            
        }
            
        if (!$item->check()) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Category check failed.  Controls fields included.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
            
        if (!$item->store()) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The Category has not been stored.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
            
        $structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
        return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    //END CATEGORY
    
    //SECTION    
    function getSections($username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));	
		}
        
		$db =& JFactory::getDBO();
        
		$query = 'SELECT title,'
		. ' id,'
        . ' published, checked_out'
		. ' FROM #__sections WHERE scope="content"';
        
		$db->setQuery($query);
		$items = $db->loadObjectList();
        
		if ($items === null) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('No Sections found.')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));	
		}
        
		$user =& JFactory::getUser($username);
        plgXMLRPCIjoomlaHelper::getUserAid($user);
		$iduser = $user->get('id');

		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
		foreach ($items as $item)
		{
			$structArray[] = new xmlrpcval(array(
                                                 'title'	=> new xmlrpcval($item->title),
                                                 'published'	=> new xmlrpcval($item->published),
												 'checked'	=> new xmlrpcval($item->checked_out),
												 'selfid'	=> new xmlrpcval($iduser),
                                                 'id'		=> new xmlrpcval($item->id)
                                                 ), 'struct');
		}
        
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    function getSection($sectionid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
        // load the row from the db table
		$item =& JTable::getInstance('section');
		if(!$item->load($sectionid)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Section does not exist.')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
        $user =& JFactory::getUser($username);
        plgXMLRPCIjoomlaHelper::getUserAid($user);
        if($item->isCheckedOut($user->get('id'))) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The Section is already being edited.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
        $db =& JFactory::getDBO();
        $query = 'SELECT COUNT(*) FROM jos_categories WHERE section = '.$sectionid.'';
        $db->setQuery($query);
        $active = $db->loadResult();
        
		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
		$structArray[] = new xmlrpcval(array(
                                            'id'		=> new xmlrpcval($item->section),
                                            'title'	=> new xmlrpcval($item->title),
                                            'active'	=> new xmlrpcval($active),
                                            'published'		=> new xmlrpcval($item->published),
                                            'access'		=> new xmlrpcval($item->access),
                                            'description'	=> new xmlrpcval($item->description)
                                            ), 'struct');
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    
    function saveSection($idcat, $title, $text, $published, $username, $password)
	{

        global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
            
        if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
            
        $user =& JFactory::getUser($username);
        plgXMLRPCIjoomlaHelper::getUserAid( $user );
            
        // load the row from the db table
        $item =& JTable::getInstance('section');
        if(!$item->load( $idcat ) && $idcat!=0) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Section does not exist'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
            
        if($item->isCheckedOut($user->get('id'))) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The Section is already being edited'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
        $item->title	 = $title;
        $item->description = $text;
		$item->scope = "content";
        $item->published		= $published;
            
        if (!$item->check()) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Section check failed. Controls fields included.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
                
        }
            
        if (!$item->store()) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The Section has not been stored'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
        $structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
        return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
    }
    
	function deleteSection($catid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
		// load the row from the db table
		$item =& JTable::getInstance('section');
		if(!$item->load( $catid )) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Section does not exist.')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
        $user =& JFactory::getUser($username);
		plgXMLRPCIjoomlaHelper::getUserAid( $user );
		if($item->isCheckedOut($user->get('id'))) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Section is already being edited')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
        $query = 'SELECT COUNT(*)'
		. ' FROM #__categories'
		. ' WHERE section = '.$catid;
        
        $db =& JFactory::getDBO();
        
		$db->setQuery( $query );
        $count = $db->loadResult();
        
        if ($count != 0) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('This Section has at least one Category. You can not complete the operation.')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		if (!$item->delete()) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Section has not been deleted.')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}		
		
		$structArray = array();
		$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval('Ok')), 'struct');
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    //END SECTION

    //TRASH    
    function deletePostDefinitively($postid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
		// load the row from the db table
		$item =& JTable::getInstance('content');
		if(!$item->load( $postid )) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Article does not exist')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
		if (!$item->delete()) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Article has not been deleted')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}		
        
        $query = 'SELECT COUNT(*)'
        . ' FROM #__content_frontpage'
        . ' WHERE content_id = '.$postid;
        
        $db =& JFactory::getDBO();
        
        $db->setQuery( $query );
        $count = $db->loadResult();
        if($count==1) {
            $query = 'DELETE FROM #__content_frontpage WHERE content_id = '.$postid;
            $db->setQuery( $query );
            $db->query();
        }
		
		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval('Ok')), 'struct');
        return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    function resetPost($postid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
		// load the row from the db table
		$item =& JTable::getInstance('content');
		if(!$item->load( $postid )) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Article does not exist.')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
        
		$item->state = 0;
		$item->ordering = 0;
        
		if (!$item->store()) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The Article has not been restored.')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}		
		
		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval('Ok')), 'struct');
        return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    //END TRASH

    //HOME
    
    function getPostsHome ($username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Login failed'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		$db =& JFactory::getDBO();
        
        $query = 'SELECT c.id, c.title, cc.title AS category_title
		FROM #__content AS c
		LEFT JOIN #__categories AS cc ON cc.id = c.catid
		INNER JOIN #__content_frontpage AS f ON f.content_id = c.id
		WHERE c.state <> -2
		ORDER BY f.ordering';
                
		$db->setQuery($query);
		$items = $db->loadObjectList();
        
		if ($items === null) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('No Articles found.'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
		foreach ($items as $item)
		{
			$structArray[] = new xmlrpcval(array(
                                                 'title'	=> new xmlrpcval($item->title),
                                                 'category'	=> new xmlrpcval($item->category_title),
                                                 'postid'		=> new xmlrpcval($item->id)
                                                 ), 'struct');
		}
        
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    function updateHome ($items2, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Login failed'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
		
        $db =& JFactory::getDBO();
        
        $query = 'SELECT c.id, c.title, cc.title AS category_title
		FROM #__content AS c
		LEFT JOIN #__categories AS cc ON cc.id = c.catid
		INNER JOIN #__content_frontpage AS f ON f.content_id = c.id
		WHERE c.state <> -2
		ORDER BY f.ordering';
        
		$db->setQuery($query);
		$items = $db->loadObjectList();

        foreach ($items as $item)
		{
			$count = 0;
            $spia = 0;
            
            foreach ($items2 as $item2)
            {
                $count++;
                if ($item->id == $item2['postid'] && $spia == 0) {
                    $query = 'UPDATE #__content_frontpage SET ordering = '.$count.'  WHERE content_id ='.$item->id;
                    $db->setQuery( $query );
                    $db->query();
                    $spia = 1;
                }
            }
            
            if ($spia == 0) {
                //elimina
                $query = 'DELETE FROM #__content_frontpage WHERE content_id = '.$item->id;
                $db->setQuery( $query );
                $db->query();
            }
		}
        
        $query = 'SELECT COUNT(*)
        FROM #__content AS c
		INNER JOIN #__content_frontpage AS f ON f.content_id = c.id
		WHERE c.state <> -2';
        
		$db->setQuery( $query );
        $count2 = $db->loadResult();
        
        if ($count2 != $count) {
            $structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The ordering of the Home page has not been completed.'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
		
        $structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
        return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    //END HOME
    //END HOME

    //USER
    
    function saveUser($iduser, $name, $userbis, $email, $block, $send, $pwd, $username, $password)
	{
        global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
        if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }

        global $mainframe;

		// Initialize some variables
		$db			= & JFactory::getDBO();
		$me 		= & JFactory::getUser($username);
		$acl		= & JFactory::getACL();
		$MailFrom	= $mainframe->getCfg('mailfrom');
		$FromName	= $mainframe->getCfg('fromname');
		$SiteName	= $mainframe->getCfg('sitename');
        
        // load the row from the db table
        $user =& JTable::getInstance('user');
        
        $user->load($iduser);                        
        $user->name	 = $name;
        $user->username = $userbis;
        $user->email = $email;
        $user->sendEmail = $send;
        $user->block = $block;
        
        if ($pwd != ""){
            jimport('joomla.user.helper');
            $salt      = JUserHelper::genRandomPassword(32);
            $crypted   = JUserHelper::getCryptedPassword($pwd, $salt);
            $user->password   = $crypted.':'.$salt;
        }
        
        if ($iduser == 0) {
            $user->usertype = "Registered";
			$user->gid = "18";
        }
        
        if (!$user->check()) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('User check failed. Controls fields included (e-mail maybe already used).'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
       
		$original_gid = $user->get('gid');

		$objectID 	= $acl->get_object_id( 'users', $user->get('id'), 'ARO' );
		$groups 	= $acl->get_object_groups( $objectID, 'ARO' );
		$this_group = strtolower( $acl->get_group_name( $groups[0], 'ARO' ) );

		if ( $iduser == $me->get( 'id' ) && $block == 1 )
		{
			$structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('You cannot block Yourself!'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
		else if ( ( $this_group == 'super administrator' ) && $block == 1 ) {
			$structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('You cannot block a Super Administrator!'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
		else if ( ( $this_group == 'administrator' ) && ( $me->get( 'gid' ) == 24 ) && $iduser != $me->get( 'id' ) )
		{
			$msg = JText::_( 'You cannot edit another Administrator!' );
			$structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_($msg))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
		else if ( ( $this_group == 'super administrator' ) && ( $me->get( 'gid' ) != 25 ) )
		{
			$structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('You cannot edit a Super Administrator!'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}

        if (!$user->store()) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The User was not been stored.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
		if ($iduser==0)
		{
			$adminEmail = $me->get('email');
			$adminName	= $me->get('name');

			$subject = 'New User Details ';
			$message = 'Hello '.$user->name.', You have been added as a User to '.$SiteName.' by an Administrator. This e-mail contains your username and password to log in to '.JURI::root().'.   ---- Username: '.$user->username.' ----  Password: '.$pwd.' ---- Please do not respond to this message as it is automatically generated and is for information purposes only.';

			if ($MailFrom != '' && $FromName != '')
			{
				$adminName 	= $FromName;
				$adminEmail = $MailFrom;
			}
			JUtility::sendMail( $adminEmail, $adminName, $user->email, $subject, $message );
		}
		
        $structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
        return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    function deleteUser($iduser, $username, $password)
	{
        global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
        if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
        // load the row from the db table
        $item =& JTable::getInstance('user');                    
        
        if (!$item->load($iduser)) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The User does not exist.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
        global $mainframe;

		$me 		= & JFactory::getUser($username);
		$acl		= & JFactory::getACL();
        
        $original_gid = $item->get('gid');

		$objectID 	= $acl->get_object_id( 'users', $item->get('id'), 'ARO' );
		$groups 	= $acl->get_object_groups( $objectID, 'ARO' );
		$this_group = strtolower( $acl->get_group_name( $groups[0], 'ARO' ) );
		
		if ( $this_group == 'super administrator' )
		{
			$structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('You cannot delete a Super Administrator')), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
		else if ( $iduser == $me->get( 'id' ) )
		{
			$structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('You cannot delete Yourself!')), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
		else if ( ( $this_group == 'administrator' ) && ( $me->get( 'gid' ) == 24 ) )
		{
			$structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('You cannot delete an Administrator')), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
		
        if (!$item->delete()) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('The User has not been deleted.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
		$query = 'UPDATE #__content SET checked_out = 0, checked_out_time = "0000-00-00 00:00:00" WHERE checked_out ='.$iduser;
		$db =& JFactory::getDBO();
		$db->setQuery( $query );
        $db->query();
		$query = 'UPDATE #__sections SET checked_out = 0, checked_out_time = "0000-00-00 00:00:00" WHERE checked_out ='.$iduser;
		$db->setQuery( $query );
        $db->query();	
		$query = 'UPDATE #__categories SET checked_out = 0, checked_out_time = "0000-00-00 00:00:00" WHERE checked_out ='.$iduser;
		$db->setQuery( $query );
        $db->query();
		
        $structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Completed with Success'))), 'struct');
        return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        
        
	}
    
    function getUsers($username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Login failed'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
        $user =& JFactory::getUser($username);
		plgXMLRPCIjoomlaHelper::getUserAid( $user );
        
		if ($user->get('gid') < 24) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('You do not have administrative privileges.'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		$db =& JFactory::getDBO();
        
		if ($user->get('gid') == 24) {
			$query = 'SELECT name,'
				. ' id, username'
				. ' FROM #__users WHERE gid <> 25 ORDER BY name';
			}
			else {
				$query = 'SELECT name,'
					. ' id, username'
					. ' FROM #__users ORDER BY name';
			}
        
		$db->setQuery($query);
		$items = $db->loadObjectList();
        
		if ($items === null) {
            $structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('No users found.'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
		foreach ($items as $item)
		{
			$structArray[] = new xmlrpcval(array(
                                                 'id'	=> new xmlrpcval($item->id),
                                                 'username'	=> new xmlrpcval($item->username),
                                                 'title'		=> new xmlrpcval($item->name)
                                                 ), 'struct');
		}
        
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    function getUser($userid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Login failed'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		// load the row from the db table
		$item =& JTable::getInstance('user');
		if(!$item->load( $userid )) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('The User does not exist.')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));			
		}
		
		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
		$structArray[] = new xmlrpcval(array(
                                             'id'	=> new xmlrpcval($item->id),
                                             'username'	=> new xmlrpcval($item->username),
                                             'name'	=> new xmlrpcval($item->name),
                                             'lastvisitDate'	=> new xmlrpcval($item->lastvisitDate),
                                             'email'	=> new xmlrpcval($item->email),
                                             'block'	=> new xmlrpcval($item->block),
                                             'sendEmail'	=> new xmlrpcval($item->sendEmail),
                                             'usertype'	=> new xmlrpcval($item->usertype),
                                             'registerDate'		=> new xmlrpcval($item->registerDate)
                                             ), 'struct');
        
        
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    //END USER

    //MAIL
    function getGroups($username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Login failed'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
        $user =& JFactory::getUser($username);
		plgXMLRPCIjoomlaHelper::getUserAid( $user );
        if ($user->get('gid') < 25) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('You do not have super-administrative privileges.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
		$acl =& JFactory::getACL();
        jimport( 'joomla.html.html' );
        $gtree = array(JHTML::_('select.option',  0, '- '. JText::_( 'All User Groups' ) .' -' ));
        
        $gtree = array_merge( $gtree, $acl->get_group_children_tree( null, 'users', false ) );
        
		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
		foreach ($gtree as $item)
		{
			$structArray[] = new xmlrpcval(array(
                                                 'id'	=> new xmlrpcval($item->value),
                                                 'name'	=> new xmlrpcval($item->text)
                                                 ), 'struct');
		}
        
		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    function sendMail($gou, $child, $subject, $message_body, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
        global $mainframe;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Login failed'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
        $user =& JFactory::getUser($username);
		plgXMLRPCIjoomlaHelper::getUserAid( $user );
        if ($user->get('gid') < 25) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('You do not have super-administrative privileges.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
        $db					=& JFactory::getDBO();
        $user 				=& JFactory::getUser();
        $acl 				=& JFactory::getACL();
        
        $mode				= 1;
        $bcc				= 1;       
        
        if ($child == 1){
            $recurse = "RECURSE";
        }
        else {
            $recurse = "NO_RECURSE";
        }
        
        // Check for a message body and subject
        if ($message_body=="" || $subject=="") {
            $structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Check failed!'))), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
        // get users in the group out of the acl
        $to = $acl->get_group_objects( $gou, 'ARO', $recurse );
        JArrayHelper::toInteger($to['users']);
              
        // Get all users email and group except for senders
        $query = 'SELECT email'
        . ' FROM #__users'
        . ' WHERE id != '.(int) $user->get('id')
        . ( $gou != 0 ? ' AND id IN (' . implode( ',', $to['users'] ) . ')' : '' )
        ;
        
        $db->setQuery( $query );
        $rows = $db->loadObjectList();
        
        // Check to see if there are any users in this group before we continue
        if ( ! count($rows) ) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('No users could be found in this group.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
        
        $mailer =& JFactory::getMailer();
        
        // Build e-mail message format
        $mailer->setSender(array($mainframe->getCfg('mailfrom'), $mainframe->getCfg('fromname')));
        $mailer->setSubject(stripslashes( $subject));
        $mailer->setBody($message_body);
        $mailer->IsHTML($mode);
        
        // Add recipients
        
        if ( $bcc ) {
            foreach ($rows as $row) {
                $mailer->addBCC($row->email);
            }
            $mailer->addRecipient($mainframe->getCfg('mailfrom'));
        }else {
            foreach ($rows as $row) {
                $mailer->addRecipient($row->email);
            }
        }
        
        // Send the Mail
        $rs	= $mailer->Send();
        
        // Check for an error
        if ( JError::isError($rs) ) {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval(JText::_('Error sending E-Mails.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        } else {
            $structArray = array();
            $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_(count($rows).' E-Mails have been sended.'))), 'struct');
            return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
	}
    //END MAIL
	
    //HELPER
    
    function uploadImage($username, $password, $filename, $filecontents)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		if(!plgXMLRPCIjoomlaHelper::authenticateUser($username, $password)) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		jimport('joomla.filesystem.file');
		$imagePath = JPATH_SITE.DS.'images'.DS.'stories'.DS.'ijoomla';
		$return = JFile::write($imagePath.DS.$filename, $filecontents);
        
		$structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval(JText::_('Ok'))), 'struct');
        return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        
	}
    
    function testConnection($username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        
		// Get the global JAuthentication object
		jimport( 'joomla.user.authentication');
		$auth = & JAuthentication::getInstance();
		$credentials = array( 'username' => $username, 'password' => $password );
		$options = array();
		$response = $auth->authenticate($credentials, $options);
        
        if ($response->status!=1) {
			$structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval('Login Failed')), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
		}
        
		$user =& JFactory::getUser($username);
        plgXMLRPCIjoomlaHelper::getUserAid($user);
        if ($user->get('gid') < 23) {
            $structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval("Login Correct, but you don't have Administrative privileges.")), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
		
		if ($user->get('block') == 1) {
            $structArray = array();
			$structArray[] = new xmlrpcval(array('result' => new xmlrpcval('0'), 'message' => new xmlrpcval("Login Correct, but are not enabled. Contact Administrator.")), 'struct');
			return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
        }
		
        $structArray = array();
        $structArray[] = new xmlrpcval(array('result' => new xmlrpcval('1'), 'message' => new xmlrpcval("Ok")), 'struct');
        return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}
    
    //END HELPER


}

class plgXMLRPCIjoomlaHelper
{
	function getUserAid( &$user ) {

		$acl = &JFactory::getACL();

		//Get the user group from the ACL
		$grp = $acl->getAroGroup($user->get('id'));

		// Mark the user as logged in
		$user->set('guest', 0);
		$user->set('aid', 1);

		// Fudge Authors, Editors, Publishers and Super Administrators into the special access group
		if ($acl->is_group_child_of($grp->name, 'Registered')      ||
			$acl->is_group_child_of($grp->name, 'Public Backend')) {
 			$user->set('aid', 2);
 		}
	}

	function authenticateUser($username, $password)
	{
		// Get the global JAuthentication object
		jimport( 'joomla.user.authentication');
		$auth = & JAuthentication::getInstance();
		$credentials = array( 'username' => $username, 'password' => $password );
		$options = array();
		$response = $auth->authenticate($credentials, $options);
        $user =& JFactory::getUser($username);
        plgXMLRPCIjoomlaHelper::getUserAid($user);
        
        if ($response->status==1) {
            if ($user->get('gid') < 23) {
                return 0;
            }
            else return 1;
        }
        return 0;
	}

}
