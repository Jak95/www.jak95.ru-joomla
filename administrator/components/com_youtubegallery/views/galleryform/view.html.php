<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');



error_reporting(E_ALL); 
/**
 * Youtube Gallery View
 */
class YoutubeGalleryViewGalleryForm extends JView
{
        /**
         * display method of Youtube Gallery view
         * @return void
         */
        public function display($tpl = null) 
        {
                //echo 'ddd';
                // get the Data
                //echo 'dddzz';
                $form = $this->get('Form');
                //echo 's1';
                //echo 'dddb';
                $item = $this->get('Item');
                ///echo 's2';
                //echo 'dddc';
                $script = $this->get('Script');

                // Check for errors.
                //echo 's3';
                if (count($errors = $this->get('Errors'))) 
                {
                       // echo 'ddd1';
                       echo 's4';
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }
                
                //echo 'ddda';
                // Assign the Data
                //echo 's5';
                $this->form = $form;
                //echo 's6';
                $this->item = $item;
                //echo 's7';
                $this->script = $script;

                //echo '*1';
                // Set the toolbar
                $this->addToolBar();
 
                // Display the template
                //echo 'ddd2';
                parent::display($tpl);
                
                // Set the document
                //$this->setDocument();

        }
 
        /**
         * Setting the toolbar
         */
        protected function addToolBar() 
        {
                JRequest::setVar('hidemainmenu', true);
                $isNew = ($this->item->id == 0);
                JToolBarHelper::title($isNew ? JText::_('COM_YOUTUBEGALLERY_NEW') : JText::_('COM_YOUTUBEGALLERY_EDIT'));
                JToolBarHelper::apply('galleryform.apply');
                JToolBarHelper::save('galleryform.save');
                JToolBarHelper::cancel('galleryform.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
        }
        
        /**
        * Method to set up the document properties
        *
        * @return void
        */
        protected function setDocument() 
        {
                $isNew = ($this->item->id < 1);
                $document = JFactory::getDocument();
                $document->setTitle($isNew ? JText::_('COM_YOUTUBEGALLERY_NEW') : JText::_('COM_YOUTUBEGALLERY_EDIT'));
                $document->addScript(JURI::root() . $this->script);
                $document->addScript(JURI::root() . "/administrator/components/com_youtubegallery/views/galleryform/submitbutton.js");
                JText::script('COM_YOUTUBEGALLERY_GALLERYFORM_ERROR_UNACCEPTABLE');
        }
}


?>