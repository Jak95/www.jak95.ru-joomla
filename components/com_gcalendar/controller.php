<?php
/**
 * GCalendar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GCalendar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GCalendar.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Allon Moritz
 * @copyright 2007-2011 Allon Moritz
 * @since 2.2.0
 */

jimport('joomla.application.component.controller');

class GCalendarController extends JController
{
	function display()
	{
		$hiddenView = null;
		if(JRequest::getVar('view', null) == 'event')
		$hiddenView = 'Event';
		if(JRequest::getVar('view', null) == 'day')
		$hiddenView = 'Day';
		if(JRequest::getVar('view', null) == 'jsonfeed')
		$hiddenView = 'JSONFeed';
		if(JRequest::getVar('view', null) == 'ical')
		$hiddenView = 'Ical';
		
		if($hiddenView !=null){
			$document =& JFactory::getDocument();

			$viewType	= $document->getType();
			$viewName	= JRequest::getCmd( 'view', $hiddenView );
			$viewLayout	= JRequest::getCmd( 'layout', 'default' );
				
			$this->addViewPath($this->basePath.DS.'hiddenviews');
			$view = & $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->basePath, 'layout' => $viewLayout));
			$view->addTemplatePath($this->basePath.DS.'hiddenviews'.DS.strtolower($viewName).DS.'tmpl');
		}
		parent::display();
	}
}
?>