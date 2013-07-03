<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewSecs extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null) 
	{
		$this->state		= $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		
		MAMSHelper::addSubmenu(JRequest::getVar('view'),JRequest::getCmd('extension', 'com_mams'));
		
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		$this->addToolBar();
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
	}

	protected function addToolBar() 
	{
		$canDo = MAMSHelper::getSecActions();
		$user  = JFactory::getUser();
		$state	= $this->get('State');
		JToolBarHelper::title(JText::_('COM_MAMS_MANAGER_SECS'), 'mams');
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('sec.add', 'JTOOLBAR_NEW');
		}
		if (($canDo->get('core.edit'))) {
			JToolBarHelper::editList('sec.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::custom('secs.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('secs.unpublish', 'unpublish.png', 'unpublish_f2.png','JTOOLBAR_UNPUBLISH', true);
		}
		if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'secs.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('secs.trash');
		}
		
		JHtmlSidebar::setAction('index.php?option=com_mams&view=secs');
		
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'),'filter_state',JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true));
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_ACCESS'),'filter_access',JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access')));
	}
	
	protected function getSortFields()
	{
		return array(
				's.ordering' => JText::_('JGRID_HEADING_ORDERING'),
				's.published' => JText::_('JSTATUS'),
				's.sec_id' => JText::_('JGRID_HEADING_ID'),
				's.sec_type' => JText::_('COM_MAMS_SEC_HEADING_TYPE'),
				's.sec_name' => JText::_('COM_MAMS_SEC_HEADING_NAME'),
				's.access' => JText::_('JGRID_HEADING_ACCESS'),
				's.sec_added' => JText::_('COM_MAMS_SEC_ADDED'),
				's.sec_modified' => JText::_('COM_MAMS_SEC_MODIFIED')
		);
	}
}
