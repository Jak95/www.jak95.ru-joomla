<?php
class com_jcommentsInstallerScript
{
	protected $postInstallUrl = 'index.php?option=com_jcomments&task=postinstall';

	function preflight($type, $parent)
	{
		JError::setErrorHandling(E_ALL ^ E_ERROR, 'ignore');
	}

	function install($parent)
	{
		$app = JFactory::getApplication();
		$app->setUserState('com_installer.message', '');
		$app->setUserState('com_installer.extension_message', '');
		$app->setUserState('com_installer.redirect_url', $this->postInstallUrl);
		$parent->getParent()->set('redirect_url', $this->postInstallUrl);
	}

	function update($parent)
	{
		self::clearUpdatesCache();
		self::install($parent);
	}

	function uninstall($parent)
	{
		JError::setErrorHandling(E_ALL ^ E_ERROR, 'ignore');
	}

	function postflight($type, $parent)
	{
		// update menu items (admin menu)
		$db = JFactory::getDBO();
		$db->setQuery("UPDATE `#__menu` SET title='COM_JCOMMENTS', alias='JComments', path='JComments' WHERE client_id = '1' AND link='index.php?option=com_jcomments'");
		$db->query();
		$db->setQuery("UPDATE `#__menu` SET title='COM_JCOMMENTS_COMMENTS', alias='Comments', path='JComments/Comments' WHERE client_id = '1' AND link='index.php?option=com_jcomments&task=comments'");
		$db->query();
		$db->setQuery("UPDATE `#__menu` SET title='COM_JCOMMENTS_SETTINGS', alias='Settings', path='JComments/Settings' WHERE client_id = '1' AND link='index.php?option=com_jcomments&task=settings'");
		$db->query();
		$db->setQuery("UPDATE `#__menu` SET title='COM_JCOMMENTS_SMILES', alias='Smiles', path='JComments/Smiles' WHERE client_id = '1' AND link='index.php?option=com_jcomments&task=smiles'");
		$db->query();
		$db->setQuery("UPDATE `#__menu` SET title='COM_JCOMMENTS_SUBSCRIPTIONS', alias='Subscriptions', path='JComments/Subscriptions' WHERE client_id = '1' AND link='index.php?option=com_jcomments&task=subscriptions'");
		$db->query();
		$db->setQuery("UPDATE `#__menu` SET title='COM_JCOMMENTS_CUSTOM_BBCODE', alias='Custom BBCode', path='JComments/Custom BBCode' WHERE client_id = '1' AND link='index.php?option=com_jcomments&task=custombbcodes'");
		$db->query();
		$db->setQuery("UPDATE `#__menu` SET title='COM_JCOMMENTS_BLACKLIST', alias='Blacklist', path='JComments/Blacklist' WHERE client_id = '1' AND link='index.php?option=com_jcomments&task=blacklist'");
		$db->query();
		$db->setQuery("UPDATE `#__menu` SET title='COM_JCOMMENTS_IMPORT', alias='Import', path='JComments/Import' WHERE client_id = '1' AND link='index.php?option=com_jcomments&task=import'");
		$db->query();
		$db->setQuery("UPDATE `#__menu` SET title='COM_JCOMMENTS_ABOUT', alias='About JComments', path='JComments/About JComments' WHERE client_id = '1' AND link='index.php?option=com_jcomments&task=about'");
		$db->query();

		// small hack to allow correct JComments update
		$task = JRequest::getVar('task', '');
		if ($task == 'update' || $task == 'update.update') {
			self::clearUpdatesCache();

			$app = JFactory::getApplication();
			$app->setRedirect($this->postInstallUrl);
			$app->redirect();
		}
	}

	function clearUpdatesCache()
	{
		$db = JFactory::getDBO();
		$db->setQuery('TRUNCATE TABLE #__updates');
		$db->query();
	}
}

?>