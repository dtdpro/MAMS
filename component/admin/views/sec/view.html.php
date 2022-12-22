<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewSec extends JViewLegacy
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
		$isNew = $this->item->sec_id == 0;
		JToolBarHelper::title($isNew ? JText::_('COM_MAMS_MANAGER_SEC_NEW') : JText::_('COM_MAMS_MANAGER_SEC_EDIT'), 'mams');
		$this->canDo		= MAMSHelper::getSecActions($this->item->sec_id);
		// Built the actions for new and existing records.
		if ($isNew && $this->canDo->get('core.create')) 
		{
			// For new records, check the create permission.
			JToolBarHelper::apply('sec.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('sec.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('sec.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::cancel('sec.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')) {
				JToolBarHelper::apply('sec.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('sec.save', 'JTOOLBAR_SAVE');
				if ($this->canDo->get('core.create')) {
					JToolBarHelper::custom('sec.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($this->canDo->get('core.create')) {
				JToolBarHelper::custom('sec.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('sec.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
