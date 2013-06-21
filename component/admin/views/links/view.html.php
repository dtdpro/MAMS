<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewLinks extends JViewLegacy
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
		$state	= $this->get('State');
		JToolBarHelper::title(JText::_('COM_MAMS_MANAGER_DLOADS'), 'mams');
		JToolBarHelper::addNew('link.add', 'JTOOLBAR_NEW');
		JToolBarHelper::editList('link.edit', 'JTOOLBAR_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('links.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('links.unpublish', 'unpublish.png', 'unpublish_f2.png','JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::divider();
		if ($state->get('filter.state') == -2) {
			JToolBarHelper::deleteList('', 'links.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else  {
			JToolBarHelper::trash('links.trash');
		}
		
		JHtmlSidebar::setAction('index.php?option=com_mams&view=links');
		
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'),'filter_state',JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true));
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_ACCESS'),'filter_access',JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access')));
	}
	
	protected function getSortFields()
	{
		return array(
				'l.published' => JText::_('JSTATUS'),
				'l.link_title' => JText::_('COM_MAMS_LINK_HEADING_TITLE'),
				'l.link_url' => JText::_('COM_MAMS_LINK_HEADING_URL'),
				'l.access' => JText::_('JGRID_HEADING_ACCESS'),
				'l.link_added' => JText::_('COM_MAMS_LINK_ADDED'),
				'l.link_id' => JText::_('JGRID_HEADING_ID'),
				'l.link_modified' => JText::_('COM_MAMS_LINK_MODIFIED')
		);
	}
}
