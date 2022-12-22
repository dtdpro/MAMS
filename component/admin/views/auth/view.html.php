<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewAuth extends JViewLegacy
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
		
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		$this->addToolBar();

		parent::display($tpl);
	}

	protected function addToolBar() 
	{
		$jinput = JFactory::getApplication()->input;
		$jinput->set('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->auth_id == 0;
		JToolBarHelper::title($isNew ? JText::_('COM_MAMS_MANAGER_AUTH_NEW') : JText::_('COM_MAMS_MANAGER_AUTH_EDIT'), 'mams');
		// Built the actions for new and existing records.
		if ($isNew) 
		{
			// For new records, check the create permission.
			JToolBarHelper::apply('auth.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('auth.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('auth.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::cancel('auth.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::apply('auth.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('auth.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('auth.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::custom('auth.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			JToolBarHelper::cancel('auth.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
