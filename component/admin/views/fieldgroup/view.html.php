<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewFieldgroup extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	
	public function display($tpl = null) 
	{
		// get the Data
		$this->state = $this->get('State');
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		$jinput = JFactory::getApplication()->input;
		$jinput->set('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->group_id == 0;
		JToolBarHelper::title($isNew ? JText::_('COM_MAMS_MANAGER_FIELDGROUP_NEW') : JText::_('COM_MAMS_MANAGER_FIELDGROUP_EDIT'), 'mams');
		// Built the actions for new and existing records.
		if ($isNew) 
		{
			// For new records, check the create permission.
			JToolBarHelper::apply('fieldgroup.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('fieldgroup.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('fieldgroup.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::cancel('fieldgroup.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::apply('fieldgroup.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('fieldgroup.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('fieldgroup.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::custom('fieldgroup.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			JToolBarHelper::cancel('fieldgroup.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
