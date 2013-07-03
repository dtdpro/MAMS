<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewArticle extends JViewLegacy
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
		$model = $this->getModel();
		$this->addfields = $model->getAdditionalForms($this->item);
		$this->canDo	= MAMSHelper::getArticleActions($this->state->get('filter.sec'));
		
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

	protected function addToolBar() 
	{
		JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->art_id == 0;
		$canDo		= MAMSHelper::getArticleActions($this->state->get('filter.sec'), $this->item->art_id);
		JToolBarHelper::title($isNew ? JText::_('COM_MAMS_MANAGER_ARTICLE_NEW') : JText::_('COM_MAMS_MANAGER_ARTICLE_EDIT'), 'mams');
		// Built the actions for new and existing records.
		if ($isNew && (count(MAMSHelper::getAuthorisedSecs('core.create')) > 0)) 
		{
			JToolBarHelper::apply('article.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('article.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('article.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::cancel('article.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			// For new records, check the create permission.
			if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId))
			{
				JToolBarHelper::apply('article.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('article.save', 'JTOOLBAR_SAVE');
				if ($canDo->get('core.create')) {
					JToolBarHelper::custom('article.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($canDo->get('core.create')) {
				JToolBarHelper::custom('article.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('article.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
