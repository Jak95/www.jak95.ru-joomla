<?php
/**
 * 			XMLRPC Plugin Joomla
 * @version		1.0.0
 * @package		XMLRPC
 * @copyright		Copyright (C) 2007-2011 Joomler!.net. All rights reserved.
 * @license		GNU/GPL 2.0 or higher
 * @author		Joomler!.net  joomlers@gmail.com
 * @link			http://www.joomler.net
 */

/**
 * To Joomla!1.6
 * Change Name to XMLRPC Joomla
 * version 1.0.0
 *
 * Updated 2.3.4
 * fix : PHP 5.3
 * support user folder
 *
 * Updated 2.3.3
 * fix : pass-by-reference
 * support ftp mode upload
 *
 * Updated 2.3.2
 * fix : new post at restricted categories
 * fix : MTMail date for Japanese Famous Service (MTMail)
 * change : screen style of setting parameters
 *
 * Updated 2.3.1
 * fix : Same filename
 * add : overwrite parameter
 *
 * Updated 2.3.0
 * fix : Google Docs
 * add : filter user groups
 * add : support plugins of aftersave and beforesave
 *
 * Updated to 2.2.1
 * fix : Undefined Property
 *
 * Updated to 2.2.0
 * Add : Single Category Mode
 * fix : modified_by, modified
 * Supported ScribeFire of version 2.3.2
 *
 * Updated to 2.1.0
 * Add : Support Google Docs
 * Add : html_entity_decode method
 *
 * Updated to 2.0.1
 * change : Joomla! version check and call date for 1.5.x All
 *
 * Updated to 2.0.0
 * Support more movable Type XML-RPC API
 * fix access : cotent, category, section
 *
 * Thanks! Great Developers.
*/

/**
 * ABOUT jMT_API
 * @package jMT_API
 * @version 1.0a
 * @copyright Copyright (C) 2006 dex_stern. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgXMLRPCJoomla extends JPlugin
{

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage( '', JPATH_ADMINISTRATOR );
	}

	public function onGetWebServices()
	{
		return array
		(
			'blogger.getUsersBlogs' => array( 'function' => array($this, 'blogger_getUserBlogs'), 'signature' => null ),
			'blogger.getUserInfo' => array( 'function' => array($this, 'blogger_getUserInfo'), 'signature' => null ),
			'blogger.getRecentPosts' => array( 'function' => array($this, 'blogger_getRecentPosts'), 'signature' => null ),
			'blogger.newPost' => array( 'function' => array($this, 'blogger_newPost'), 'signature' => null ),
			'blogger.deletePost' => array( 'function' => array($this, 'blogger_deletePost'), 'signature' => null ),
			'blogger.editPost' => array( 'function' => array($this, 'blogger_editPost'), 'signature' => null ),
			'metaWeblog.newPost' => array( 'function' => array($this, 'mw_newPost'), 'signature' => null ),
			'metaWeblog.editPost' => array( 'function' => array($this, 'mw_editPost'), 'signature' => null ),
			'metaWeblog.getPost' => array( 'function' => array($this, 'mw_getPost'), 'signature' => null ),
			'metaWeblog.newMediaObject' => array( 'function' => array($this, 'mw_newMediaObject'), 'signature' => null ),
			'metaWeblog.getRecentPosts' => array( 'function' => array($this, 'mw_getRecentPosts'), 'signature' => null ),
			'metaWeblog.getCategories' => array( 'function' => array($this, 'mw_getCategories'), 'signature' => null ),
			'mt.getCategoryList' => array( 'function' => array($this, 'mt_getCategoryList'), 'signature' => null ),
			'mt.getPostCategories' => array( 'function' => array($this, 'mt_getPostCategories'), 'signature' => null ),
			'mt.setPostCategories' => array( 'function' => array($this, 'mt_setPostCategories'), 'signature' => null ),
			'mt.getRecentPostTitles' => array( 'function' => array($this, 'mt_getRecentPostTitles'), 'signature' => null ),
			'mt.supportedTextFilters' => array( 'function' => array($this, 'mt_supportedTextFilters'), 'signature' => null ),
			'mt.publishPost' => array( 'function' => array($this, 'mt_publishPost'), 'signature' => null ),
			'mt.getTrackbackPings' => array( 'function' => array($this, 'mt_getTrackbackPings'), 'signature' => null ),
			'mt.supportedMethods' => array( 'function' => array($this, 'mt_supportedMethods'), 'signature' => null ),
			'wp.getCategories' => array( 'function' => array($this, 'wp_getCategories'), 'signature' => null ),
			'wp.newCategory' => array( 'function' => array($this, 'wp_newCategory'), 'signature' => null )
		);
	}

	public function wp_getCategories()
	{
		global $xmlrpcerruser;

		$args		= func_get_args();

		if(func_num_args() < 3){
			return new xmlrpcresp(0, $xmlrpcerruser + 1,  JText::_('The request is illegal.'));
		}

		$username	= strval( $args[1] );
		$password	= strval( $args[2] );

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$structarray = array();

		JRequest::setVar('limit', 0);
		$model = $this->getModel('Categories');
		$categories = $model->getItems();

		if(empty($categories)){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_CATEGORY_WAS_NOT_FOUND'));
		}

		foreach($categories as $row){
			if($row->published < 1){
				if(!$user->authorise('core.edit.state', 'com_content.category.'. $row->id)){
					continue;
				}

				if(!$user->authorise('core.admin', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id')){
					continue;
				}
			}

			$array = array();

			$array['categoryId']		= new xmlrpcval( $row->id, 'string' );
			$array['parentId']		= new xmlrpcval( $row->parent_id, 'string' );
			$array['description']		= new xmlrpcval( $row->description, 'string' );
			$array['categoryDescription']		= new xmlrpcval( $row->description, 'string' );
			$array['categoryName']	= new xmlrpcval( $row->title, 'string' );
			$array['htmlUrl']		= new xmlrpcval( JRoute::_(ContentHelperRoute::getCategoryRoute($row->id)), 'string' );
			$array['rssUrl']			= new xmlrpcval( JRoute::_(ContentHelperRoute::getCategoryRoute($row->id). '&format=feed'), 'string' );

			$structarray[] = new xmlrpcval( $array, 'struct' );
		}

		return new xmlrpcresp(new xmlrpcval($structarray, 'array'));
	}

	public function wp_newCategory()
	{
		global $xmlrpcerruser;

		$args		= func_get_args();

		if(func_num_args() < 4){
			return new xmlrpcresp(0, $xmlrpcerruser + 1,  JText::_('The request is illegal.'));
		}

		$username	= strval( $args[1] );
		$password	= strval( $args[2] );
		$category	= $args[3];

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		if(!$user->authorise('core.create', 'com_content')){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		if(empty($category['name'])){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_CATEGORY_MUST_HAVE_TITLE'));
		}

		$category['title'] = $category['name'];
		unset($category['name']);

		$category['extension'] = 'com_content';
		$category['published'] = 1;

		$model = $this->getModel('Category');
		if(!$model->save($category)){
			return $this->response($model->getError());
		}

		return (new xmlrpcresp(new xmlrpcval($model->getState('category.id'), 'string')));

	}

	public function blogger_getUserBlogs()
	{
		$args		= func_get_args();

		if(func_num_args() < 3){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid		= (int)$args[0];
		$username	= $args[1];
		$password	= $args[2];

		$mt	= false;

		if(isset($args[3])){
			$mt = (boolean)$args[3];
		}

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$db =& JFactory::getDbo();
		$app = JFactory::getApplication();

		$structarray = array();

		if(!$mt){
			$site_name = $app->getCfg('sitename');
			$structarray[] = new xmlrpcval(
				array('url' => new xmlrpcval(JURI::root(), 'string'),
				'blogid' => new xmlrpcval(0, 'string'),
				'blogName' => new xmlrpcval($site_name, 'string')),
				'struct');
			return new xmlrpcresp(new xmlrpcval($structarray, 'array'));
		}

		$model = $this->getModel('Categories');
		$categories = $model->getItems();

		if(empty($categories)){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_CATEGORY_WAS_NOT_FOUND'));
		}

		foreach($categories as $row){
			if($row->published < 1){
				if(!$user->authorise('core.edit.state', 'com_content.category.'. $row->id)){
					continue;
				}

				if(!$user->authorise('core.admin', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id')){
					continue;
				}
			}
			$row->title = str_repeat(' ...', $row->level-1). $row->title;
			$structarray[] = new xmlrpcval(
				array('categoryId' => new xmlrpcval($row->id, 'string'),
				'categoryName' => new xmlrpcval($row->title, 'string')),
				'struct');
		}

		if(empty($structarray)){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_CATEGORY_WAS_NOT_FOUND'));
		}

		return new xmlrpcresp(new xmlrpcval($structarray, 'array'));
	}

	public function blogger_getUserInfo()
	{
		global $xmlrpcStruct;

		$args		= func_get_args();

		if(func_num_args() < 3){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$username	= strval( $args[1] );
		$password	= strval( $args[2] );

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$name = $user->name;
		if(function_exists('mb_convert_kana')){
			$name = mb_convert_kana($user->name, 's');
		}

		$names = explode(' ', $name);
		$firstname = $names[0];
		$lastname = trim(str_replace($firstname, '', $name));

		$struct = new xmlrpcval(
		array(
			'nickname'	=> new xmlrpcval($user->username),
			'userid'		=> new xmlrpcval($user->id),
			'url'		=> new xmlrpcval(JURI::root()),
			'email'		=> new xmlrpcval($user->email),
			'lastname'	=> new xmlrpcval($lastname),
			'firstname'	=> new xmlrpcval($firstname)
		), $xmlrpcStruct);

		return new xmlrpcresp($struct);

	}

	public function blogger_newPost()
	{
		$args		= func_get_args();

		if(func_num_args() < 6){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid		= (int)$args[1];
		$username	= strval( $args[2] );
		$password	= strval( $args[3] );
		$content	= $args[4];
		$publish	= (int)$args[5];

		return $this->mw_newPost($blogid, $username, $password, $content, $publish, true);
	}

	public function blogger_editPost()
	{
		$args		= func_get_args();

		if(func_num_args() < 6){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$postid		= (int)$args[1];
		$username	= strval( $args[2] );
		$password	= strval( $args[3] );
		$content	= $args[4];
		$publish	= (int)$args[5];

		return $this->mw_editPost($postid, $username, $password, $content, $publish, true);
	}

	public function blogger_deletePost()
	{
		global $xmlrpcBoolean;

		$args		= func_get_args();

		if(func_num_args() < 5){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$postid		= (int)$args[1];
		$username	= $args[2];
		$password	= $args[3];
		$publish	= (int)$args[4];

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$userid = intval($user->get('id'));

		$model = $this->getModel('Article');
		$row = $model->getTable();
		$result = $row->load($postid);
		if(!$result){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ITEM_WAS_NOT_FOUND'));
		}

		if(!$model->canEditState($row)){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $userid)
		{
			return $this->response(JText::sprintf('PLG_XMLRPC_JOOMLA_EDITING_OTHER_USER', $row->title));
		}

		$row->checkout((int)$userid);

		$row->ordering = 0;
		$row->state = -2;//to trash

		if (!$row->check()){
			return $this->response($row->getError());
		}

		if (!$row->store()){
			return $this->response($row->getError());
		}

		$row->checkin();

		//clear cache
		$cache = & JFactory::getCache('com_content');
		$cache->clean();

		return new xmlrpcresp(new xmlrpcval('true', $xmlrpcBoolean));
	}

	public function blogger_getRecentPosts()
	{
		$args		= func_get_args();

		if(func_num_args() < 5){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid		= (int)$args[1];
		$username	= strval( $args[2] );
		$password	= strval( $args[3] );
		$numposts	= (int)$args[4];

		return $this->mw_getRecentPosts($blogid, $username, $password, $numposts);
	}

	public function mw_newPost()
	{
		$args		= func_get_args();

		if(func_num_args() < 4){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid		= (int)$args[0];
		$username	= $args[1];
		$password	= $args[2];
		$content	= $args[3];
		if(isset($args[4]))
			$publish	= $args[4];
		$blogger	= false;
		if(isset($args[5])){
			$blogger = $args[5];
		}

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$content['catid'] = (int)$blogid;

		$data  = $this->buildData($content, $publish, $blogger);

		if($this->params->get('featured', 0)){
			$data['featured'] = 1;
		}

		$model = $this->getModel('Article');

		if($model->allowAdd($data) !== true){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		if(!$model->save($data)){
			return $this->response($model->getError());
		}

		return (new xmlrpcresp(new xmlrpcval($model->getState('article.id'), 'string')));
	}

	public function mw_editPost()
	{
		$args		= func_get_args();

		if(func_num_args() < 4){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$postid		= (int)$args[0];
		$username	= $args[1];
		$password	= $args[2];
		$content	= $args[3];
		$publish	= (int)$args[4];

		$blogger	= false;

		if(isset($args[5])){
			$blogger = $args[5];
		}

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$content['id'] = $postid;

		$data  = $this->buildData($content, $publish, $blogger);

		$model = $this->getModel('Article');

		if($model->allowEdit($data) !== true){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		if(!$model->save($data)){
			return $this->response($model->getError());
		}

		return (new xmlrpcresp(new xmlrpcval('1', 'boolean')));
	}

	public function mw_getPost()
	{
		$args		= func_get_args();

		if(func_num_args() < 3){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$postid		= (int)$args[0];
		$username	= strval( $args[1] );
		$password	= strval( $args[2] );

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$model = $this->getModel('Article');

		$data = array();
		$data['id'] = $postid;

		if($model->allowEdit($data) !== true){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		$row = $model->getItem($postid);
		if(empty($row)){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ITEM_WAS_NOT_FOUND'));
		}

		$ret = $this->buildStruct($row);

		if(!$ret[0]){
			return $this->response($ret[1]);
		}

		return new xmlrpcresp($ret[1]);
	}

	public function mw_getRecentPosts()
	{
		global $xmlrpcArray;

		$args		= func_get_args();

		if(func_num_args() < 3){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid		= (int)$args[0];
		$username	= $args[1];
		$password	= $args[2];

		$limit		= 0;

		if(isset($args[3])){
			$limit = (int)$args[3];
		}

		$mt	= false;

		if(isset($args[5])){
			$mt = (boolean)$args[5];
		}

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$blogid = (int)$blogid;
		if($blogid > 0){
			JRequest::setVar('filter_category_id', $blogid);
		}

		JRequest::setVar('limit', $limit);
		$model = $this->getModel('Articles');
//		$model->setState('list.limit', $limit);

		$userid = (int)$user->get('id');

		$temp = $model->getItems();
		$articles = array();
		if(count($temp)){
			foreach ($temp as $row)
			{
				$canEdit	= $user->authorise('core.edit', 'com_content.article.'.$row->id);
				$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $row->checked_out == $userid || $row->checked_out == 0;
				$canEditOwn	= $user->authorise('core.edit.own', 'com_content.article.'.$row->id) && $row->created_by == $userid;

				if(($canEdit || $canEditOwn) && $canCheckin){
					$res = $this->buildStruct($row, $mt);

					if ($res[0]){
						$articles[] = $res[1];
					}
				}
			}
		}

		return new xmlrpcresp(new xmlrpcval($articles, $xmlrpcArray));
	}

	public function mt_getPostCategories()
	{
		$args		= func_get_args();

		if(func_num_args() < 3){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$postid		= (int)$args[0];
		$username	= strval( $args[1] );
		$password	= strval( $args[2] );

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$postid = (int)$postid;

		$model = $this->getModel('Article');
		$row = $model->getItem($postid);
		if(!$row){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ITEM_WAS_NOT_FOUND'));
		}

		$data = array();
		$data['id'] = $row->id;
		$data['created_by'] = $row->created_by;
		if($model->allowEdit($data) !== true){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		if(empty($row->catid)){
			return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
		} else {
			$cmodel = $this->getModel('Category');
			$category = $cmodel->getItem((int)$row->catid);
			if(empty($category)){
				return $this->response(JText::_('PLG_XMLRPC_JOOMLA_CATEGORY_WAS_NOT_FOUND'));
			}

			if(!$cmodel->canEditState($category) && $category->published < 1){
				return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
			}
		}

		$structarray = array();

		$structarray[] = new xmlrpcval(
			array('categoryName' => new xmlrpcval($category->title, 'string'),
			'categoryId' => new xmlrpcval($category->id, 'string'),
			'isPrimary' => new xmlrpcval(1, 'boolean')),
			'struct');

		return new xmlrpcresp(new xmlrpcval($structarray, 'array'));
	}

	public function mt_setPostCategories()
	{
		$args		= func_get_args();

		if(func_num_args() < 4){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid		= (int)$args[0];
		$username	= strval( $args[1] );
		$password	= strval( $args[2] );
		$categories	= $args[3];

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$blogid = (int)$blogid;

		$model = $this->getModel('Article');
		$row = $model->getTable();
		$result = $row->load($blogid);
		if(!$result){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ITEM_WAS_NOT_FOUND'));
		}

		if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id')){
			return $this->response(JText::sprintf('PLG_XMLRPC_JOOMLA_EDITING_OTHER_USER', $row->title));
		}

		$data = array();
		$data['id'] = $row->id;
		$data['created_by'] = $row->created_by;
		if($model->allowEdit($data) !== true){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		$row->checkout((int)$user->id);

		$cmodel = $this->getModel('Category');

		if($blogid && is_array($categories) && count($categories)){
			$catid = 0;
			$primary_catid = 0;
			for($i = 0; $i < count($categories); $i++){
				if(!isset($categories[$i]['categoryId'])){
					continue;
				}
				if(isset($categories[$i]['categoryId']) && !(int)$categories[$i]['categoryId']){
					continue;
				}

				$tempcatid = (int)$categories[$i]['categoryId'];

				if($catid == 0){
					$catid = $tempcatid;
				}

				if(isset($categories[$i]['isPrimary']) && $categories[$i]['isPrimary']){
					$primary_catid = $tempcatid;
				}
			}

			if($catid && $primary_catid && $primary_catid !== $catid){
				$catid = $primary_catid;
			}

			if(!$catid){
				return $this->response(JText::_('PLG_XMLRPC_JOOMLA_CORRECT_CATEGORY'));
			}

			$row->catid = $catid;

			if (!$row->check()){
				return $this->response($row->getError());
			}

			//Double
//			$row->version++;

			$dispatcher =& JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');

			$result = $dispatcher->trigger('onBeforeContentSave', array( & $row, false));
			if(in_array(false, $result, true)) {
				return $this->response($row->getError());
			}

			if (!$row->store()){
				return $this->response($row->getError());
			}

			$row->reorder("catid = " . (int) $row->catid);

			$dispatcher->trigger('onAfterContentSave', array( & $row, false));

			//clear cache
			$cache = & JFactory::getCache('com_content');
			$cache->clean();

		}

		$row->checkin();

		return (new xmlrpcresp(new xmlrpcval('1', 'boolean')));
	}

	public function mt_getRecentPostTitles()
	{
		$args		= func_get_args();

		if(func_num_args() < 4){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid		= (int)$args[0];
		$username	= strval( $args[1] );
		$password	= strval( $args[2] );

		$limit = 0;

		if(isset($args[4])){
			$limit		= (int)$args[4];
		}

		return $this->mw_getRecentPosts($blogid, $username, $password, $limit, true);
	}

	public function mt_getCategoryList()
	{
		$args		= func_get_args();

		if(func_num_args() < 3){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid		= (int)$args[0];
		$username	= strval( $args[1] );
		$password	= strval( $args[2] );

		return $this->blogger_getUserBlogs($blogid, $username, $password, true);
	}

	public function mt_supportedTextFilters()
	{
		return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
	}

	public function mt_publishPost()
	{
		$args		= func_get_args();

		if(func_num_args() < 3){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$postid		= (int)$args[0];
		$username	= strval( $args[1] );
		$password	= strval( $args[2] );

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$model = $this->getModel('Article');
		$row = $model->getTable();
		$result = $row->load($postid);
		if(!$result){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ITEM_WAS_NOT_FOUND'));
		}

		if(!$user->authorise('core.edit.state', 'com_content.article.'.$item->id)){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id')){
			return $this->response(JText::sprintf('PLG_XMLRPC_JOOMLA_EDITING_OTHER_USER', $row->title));
		}

		$data = array();
		$data['id'] = $row->id;
		$data['created_by'] = $row->created_by;
		if($model->allowEdit($data) !== true){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		$row->checkout((int)$user->id);

		$row->state = 1;
		if (!$row->check()){
			return $this->response($row->getError());
		}

		$article->version++;

		if (!$row->store()){
			return $this->response($row->getError());
		}

		$row->checkin();

		//clear cache
		$cache = & JFactory::getCache('com_content');
		$cache->clean();

		return (new xmlrpcresp(new xmlrpcval('1', 'boolean')));
	}

	public function mt_getTrackbackPings()
	{
		$args		= func_get_args();

		if(func_num_args() < 1){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid		= (int)$args[0];

		//pingIP, pingURL, pingTitle
		return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
	}

	public function mt_supportedMethods()
	{
		return (new xmlrpcresp(new xmlrpcval(array(), 'array')));
	}

	public function mw_newMediaObject()
	{
		$args		= func_get_args();

		if(func_num_args() < 4){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ILLEGAL_REQUEST'));
		}

		$blogid		= (int)$args[0];
		$username	= strval( $args[1] );
		$password	= strval( $args[2] );
		$file_struct	= $args[3];

		$user = $this->authenticateUser($username, $password);

		if (!$user)
		{
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_LOGIN_WAS_NOT_ABLE'));
		}

		$model = $this->getModel('Article');
		$row = $model->getTable();
		$result = $row->load($blogid);
		if(!$result){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_ITEM_WAS_NOT_FOUND'));
		}

		if (!$user->authorise('core.manage', 'com_checkin') && $row->checked_out > 0 && $row->checked_out != $user->get('id')){
			return $this->response(JText::sprintf('PLG_XMLRPC_JOOMLA_EDITING_OTHER_USER', $row->title));
		}

		$data = array();
		$data['id'] = $row->id;
		$data['created_by'] = $row->created_by;
		if($model->allowEdit($data) !== true){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_DO_NOT_HAVE_AUTH'));
		}

		$file = $file_struct['bits'];
		$file_name = $file_struct['name'];

		$params = JComponentHelper::getParams('com_media');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		if(empty($file)){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_FILE_EMPTY'));
		}
		//File size check
		$maxSize = (int) ($params->get('upload_maxsize', 0) * 1024 * 1024);
		if($maxSize && strlen($file) > $maxSize){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_NOT_ALLOWED_FILE_SIZE'));
		}

		if(empty($file_name)){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_FILE_EMPTY'));
		}

		//filename check
		$temp = pathinfo($file_name);
		$file_name = trim($temp['basename']);
		if(empty($file_name)){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_FILENAME_EMPTY'));
		}

		$file_name = strtolower(JFile::makeSafe($file_name));
		$ext = JFile::getExt($file_name);

		$allowable = explode(',', $params->get('upload_extensions'));
		$ignored = explode(',', $params->get('ignore_extensions'));
		$images = explode(',', $params->get('image_extensions'));

		if (!in_array($ext, $allowable) && !in_array($ext,$ignored)){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_NOT_ALLOWED_FILETYPE'));
		}

		require_once JPATH_ADMINISTRATOR.'/components/com_media/helpers/media.php';

		$images_path = str_replace('/', DS, JPATH_ROOT. DS. $params->get('image_path', 'images'));
		$file_path = str_replace('/', DS, JPATH_ROOT. DS. $params->get('file_path', 'images'));

		if(in_array($ext, $images)){
			$destination = $images_path;
		} else {
			$destination = $file_path;
		}

		$destination .= DS;

		if($this->params->get('userfolder')){
			$userfolder = JFile::makeSafe($username);
			if(!empty($userfolder)){
				$destination .= $userfolder;
				if(!JFolder::exists($destination)){
					if(!JFolder::create($destination)){
						return $this->response(JText::_('PLG_XMLRPC_JOOMLA_NOT_ABLE_TO_CREATE_FOLDER'));
					}
				}

				if(!JFile::exists($destination. DS. 'index.html')){
					$html = '<html><body></body></html>';
					JFile::write($destination. DS. 'index.html', $html);
				}

				$destination .= DS;
			}
		}

		if(file_exists($destination . $file_name) && (/*!isset($file_struct['overwrite']) || !$file_struct['overwrite'] ||*/ !$this->params->get('overwrite'))){
			$nameonly = str_replace(strrchr($file_name, '.'), '', $file_name);//for 1.5.10 or under
			$nameonly .= '_'. JUtility::getHash(microtime()*1000000);
			$file_name = JFile::makeSafe($nameonly. '.'. $ext);
		}

		if(!JFile::write($destination. $file_name, $file)){
			return $this->response(JText::_('PLG_XMLRPC_JOOMLA_NOT_ABLE_TO_UPLOAD_FILE'));
		}

		if(!file_exists($destination . $file_name)){
			return $this->response(JText::sprintf('PLG_XMLRPC_JOOMLA_NOT_ABLE_TO_UPLOAD_FILE'));
		}

		$url = JURI::root(true). str_replace(array(JPATH_ROOT, DS), array('', '/'), $destination. $file_name);

		$responce_struct = array('url' => new xmlrpcval($url, 'string'));

		return (new xmlrpcresp(new xmlrpcval($responce_struct, 'struct')));
	}

	protected function authenticateUser($username, $password)
	{
		jimport( 'joomla.user.authentication');
		$auth = & JAuthentication::getInstance();
		$credentials['username'] = $username;
		$credentials['password'] = $password;
		$authuser = $auth->authenticate($credentials, null);

		if($authuser->status == JAUTHENTICATE_STATUS_FAILURE || empty($authuser->username) || empty($authuser->password) || empty($authuser->email)){
			return false;
		}

		$user =& JUser::getInstance($authuser->username);
		//Check Status
		if(empty($user->id) || $user->block || !empty($user->activation)){
			return false;
		}

		JFactory::getSession()->set('user', $user);

		return $user;
	}

	protected function getCatTitle($id)
	{
		$db =& JFactory::getDBO();
		if(!$id){
			return;
		}
		$query = 'SELECT title'
		. ' FROM #__categories'
		. ' WHERE id = '. (int)$id
		;
		$db->setQuery( $query );
		return $db->loadResult();
	}

	protected function GoogleDocsToContent(&$content)
	{

		if(is_array($content) || (is_string($content) && strpos($content, 'google_header') === false)){
			return;
		}

		//Header title
		$headerregex = '/<div.+?google_header[^>]+>(.+?)<\/div>/is';
		//Old page break;
		$oldpbregex = '/<p.+?page-break-after[^>]+>.*?<\/p>/is';
		//Horizontal line
		$hrizonregex = '/<hr\s+?size="2"[^>]*?>/is';
		//New page break;
		$newpbregex = '/<hr\s+?class="pb"[^>]*?>/is';

		$match = array();
		if(preg_match($headerregex, $content, $match)){
			$title = trim($match[1]);
			$introandfull = preg_replace($headerregex, '', $content);
		} else {
				$title = JString::substr( $content, 0, 30 );
				$introandfull = str_replace($title, '', $content);
		}

		$text = preg_split($oldpbregex, $introandfull, 2, PREG_SPLIT_NO_EMPTY);
		$introtext = '';
		$fulltext = '';
		if(count($text) > 1){
			$introtext = trim($text[0]);
			$fulltext = trim($text[1]);
		} else {

			//new
			if(!$this->params->get('readmore')){
				//Horizontal line
				$regex = $hrizonregex;
			} else {
				//Page break
				$regex = $newpbregex;
			}

			//first horizontal line or pagebreak
			$text = preg_split($regex, $introandfull, 2, PREG_SPLIT_NO_EMPTY);
			if(count($text) > 1){
				$introtext = trim($text[0]);
				$fulltext = trim($text[1]);
			} else {
				$introtext = trim($introandfull);
			}
		}

		if($this->params->get('pagebreak')){
			$count = 2;
			//for pagebreak
			$text = preg_split($newpbregex, $introtext, -1, PREG_SPLIT_NO_EMPTY);
			if(count($text) > 1){
				$introtext = '';
				for($i = 0; $total = count($text), $i < $total;$i++){
					$alt = JText::sprintf('PAGEBREAK', $count);
					$count++;
					$introtext .= $text[$i];
					if($i < ($total -1)){
						$introtext .= '<hr title="'. $alt. '" alt="'. $alt. '" class="system-pagebreak" />';
					}
				}
			}

			if(!empty($fulltext)){
				$text = preg_split($newpbregex, $fulltext, -1, PREG_SPLIT_NO_EMPTY);
				if(count($text) > 1){
					$fulltext = '';
					for($i = 0; $total = count($text), $i < $total;$i++){
						$alt = JText::sprintf('PAGEBREAK', $count);
						$count++;
						$fulltext .= $text[$i];
						if($i < ($total -1)){
							$fulltext .= '<hr title="'. $alt. '" alt="'. $alt. '" class="system-pagebreak" />';
						}
					}
				}
			}
		}

		//b to br and escape
		$replace_from = array('<b>', '</b>', '<br>');
		$replace_to = array('<strong>', '</strong>', '<br />');
		$title = htmlspecialchars(strip_tags($title), ENT_QUOTES, 'UTF-8');
		$introtext = str_replace($replace_from, $replace_to, $introtext);
		$fulltext = str_replace($replace_from, $replace_to, $fulltext);

		$content = array();
		$content['title']			= $title;
		$content['description']		= $introtext;
		$content['mt_text_more']	= $fulltext;
		return;
	}

	protected function buildStruct($row, $mt=false)
	{
		$user = JFactory::getUser();

		$date = iso8601_encode(strtotime($row->created), 0);

		if($mt){
			$xmlArray = array(
				'userid'			=> new xmlrpcval( $row->created_by, 'string' ),
				'dateCreated'		=> new xmlrpcval( $date , 'dateTime.iso8601' ),
				'postid'			=> new xmlrpcval( $row->id, 'string' ),
				'title'			=> new xmlrpcval( $row->title, 'string' ),
			);
		} else {

			$link	= JRoute::_(ContentHelperRoute::getArticleRoute($row->id, $row->catid), false, 2);
			$xmlArray = array(
				'userid'			=> new xmlrpcval( $row->created_by, 'string' ),
				'dateCreated'		=> new xmlrpcval( $date , 'dateTime.iso8601' ),
				'postid'			=> new xmlrpcval( $row->id, 'string' ),
				'description'		=> new xmlrpcval( $row->introtext, 'string' ),
				'title'			=> new xmlrpcval( $row->title, 'string' ),
				'wp_slug' => new xmlrpcval( $row->alias, 'string'),
				'mt_basename' => new xmlrpcval( $row->alias, 'string'),
				'categories'		=> new xmlrpcval( array( new xmlrpcval($row->category_title, 'string') ) , 'array' ),
				'link'			=> new xmlrpcval( $link, 'string' ),
				'permaLink'		=> new xmlrpcval( $link, 'string' ),
				'mt_excerpt'		=> new xmlrpcval( $row->metadesc, 'string' ),
				'mt_text_more'	=> new xmlrpcval( $row->fulltext, 'string' ),
				'mt_allow_comments'	=> new xmlrpcval( '1', 'int'),
				'mt_allow_pings'	=> new xmlrpcval( '0', 'int' ),
				'mt_convert_breaks'	=> new xmlrpcval( '', 'string' ),
				'mt_keywords'		=> new xmlrpcval( $row->metakey, 'string' )
			);
		}

		$xmlObject = new xmlrpcval($xmlArray, 'struct');
		return array(true, $xmlObject);
	}

	protected function buildData($content, $publish, $blogger=false)
	{
		$date = JFactory::getDate();
		$created = $date->toMySQL();

		$user = JFactory::getUser();
		$userid = intval( $user->get('id') );

		if($blogger){
			$this->GoogleDocsToContent($content);
		}

		if(!isset($content['description'])){
			$content['description'] = '';
		}

		$content['articletext'] = $content['description'];
		unset($content['description']);

		//alias
		if(isset($content['mt_basename'])  && !empty($content['mt_basename'])){
			$content['alias'] = $content['mt_basename'];
			unset($content['mt_basename']);
		} else if(isset($content['wp_slug'])  && !empty($content['wp_slug'])){
			$content['alias'] = $content['wp_slug'];
			unset($content['wp_slug']);
		}

		if(!isset($content['mt_text_more'])){
			$content['mt_text_more'] = '';
		}

		$content['mt_text_more'] = trim($content['mt_text_more']);

		if(JString::strlen($content['mt_text_more']) < 1){
			$temp = explode('<!--more-->', $content['articletext']);//for MetaWeblog
			if(count($temp) > 1){
				$content['articletext'] = $temp[0]. '<hr id="system-readmore" />';
				$content['articletext'] .= $temp[1];
			}
		} else {
			$content['articletext'] .= '<hr id="system-readmore" />';
			$content['articletext'] .= $content['mt_text_more'];
		}

		unset($content['mt_text_more']);

		if(!isset($content['mt_keywords'])){
			$content['mt_keywords'] = '';
		}

		$content['metakey'] = $content['mt_keywords'];

		if(!isset($content['mt_excerpt'])){
			$content['mt_excerpt'] = '';
		}

		$content['metadesc'] = $content['mt_excerpt'];

		$content['state'] = 0;

		if ($publish){
			$content['state'] = 1;
		}

		$content['language'] = $this->params->get('language', '*');

		//date
		if(isset($content['dateCreated_gmt'])){
			$date = JFactory::getDate(iso8601_decode($content['dateCreated'], 0));
			$content['created']  = $content['publish_up'] = $date->toMySQL();
		} else if(isset($content['dateCreated'])){
			$date = JFactory::getDate(iso8601_decode($content['dateCreated'], 0));
			$content['created']  = $content['publish_up'] = $date->toMySQL();
		}

		if(empty($content['id']) && empty($content['created'])){
			$content['created'] = JFactory::getDate()->toMySQL();
		}

		return $content;
	}

	protected function getModel($type, $prefix='XMLRPCModel', $config=array())
	{
		return JModel::getInstance($type, $prefix, $config);
	}

	protected function response($msg)
	{
		global $xmlrpcerruser;
		return new xmlrpcresp(0, $xmlrpcerruser + 1, $msg);
	}
}
