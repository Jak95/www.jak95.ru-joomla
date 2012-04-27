<?php
/**
 * @package	JM
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JMViewCpanel extends JMView {
	
	public function display($tpl = null) {
		
		if ($this->routeLayout($tpl)) :
			return;
		endif;
		
		$this->generateToolbar();
		
		$views 		= $this->getMainViews();
				
		$this->assignRef('views', $views);
		$this->assignRef('modified', $modified);
		
		parent::display($tpl);
	}
	
	private function generateToolbar() {
		JToolBarHelper::title(JText::_('COM_JM').': '.JText::_('COM_JM_CONTROL_PANEL'));
		JToolBarHelper::preferences('com_jm', 500, 500);
	}
	
}
