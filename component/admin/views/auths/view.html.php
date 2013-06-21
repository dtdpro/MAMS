<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewAuths extends JViewLegacy
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
		
		// Set the toolbar
		$this->addToolBar();
		$this->sidebar = JHtmlSidebar::render();	

		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		$state	= $this->get('State');
		JToolBarHelper::title(JText::_('COM_MAMS_MANAGER_AUTHS'), 'mams');
		JToolBarHelper::addNew('auth.add', 'JTOOLBAR_NEW');
		JToolBarHelper::editList('auth.edit', 'JTOOLBAR_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('auths.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('auths.unpublish', 'unpublish.png', 'unpublish_f2.png','JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::divider();
		if ($state->get('filter.state') == -2) {
			JToolBarHelper::deleteList('', 'auths.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else  {
			JToolBarHelper::trash('auths.trash');
		}
		
		JHtmlSidebar::setAction('index.php?option=com_mams&view=auths');
		
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'),'filter_state',JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true));
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_ACCESS'),'filter_access',JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access')));
		JHtmlSidebar::addFilter(JText::_('COM_MAMS_SELECT_SEC'),'filter_sec',JHtml::_('select.options', MAMSHelper::getSections("author"), 'value', 'text', $this->state->get('filter.sec')));
	}
	
	protected function getSortFields()
	{
		return array(
				'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
				'a.published' => JText::_('JSTATUS'),
				'a.auth_added' => JText::_('COM_MAMS_AUTH_HEADING_ADDED'),
				'a.auth_modified' => JText::_('COM_MAMS_AUTH_HEADING_MODIFIED'),
				'a.auth_fname' => JText::_('COM_MAMS_AUTH_HEADING_FNAME'),
				'a.auth_lname' => JText::_('COM_MAMS_AUTH_HEADING_LNAME'),
				'a.access' => JText::_('JGRID_HEADING_ACCESS'),
				'a.auth_id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
