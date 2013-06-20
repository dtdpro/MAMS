<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');


class MAMSViewArtAuths extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $arttitle;
	
	function display($tpl = null) 
	{
		$this->state		= $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->arttitle = $this->get('ArticleTitle');
		
		MAMSHelper::addArtDDSubmenu(JRequest::getVar('view'),$this->arttitle);
		
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
		JToolBarHelper::title(JText::_('COM_MAMS_MANAGER_ARTAUTHS'), 'mams');
		JToolBarHelper::addNew('artauth.add', 'COM_MAMS_TOOLBAR_ADD');
		JToolBarHelper::editList('artauth.edit', 'JTOOLBAR_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('artauths.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('artauths.unpublish', 'unpublish.png', 'unpublish_f2.png','JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::divider();
		if ($state->get('filter.state') == -2) {
			JToolBarHelper::deleteList('', 'artauths.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else  {
			JToolBarHelper::trash('artauths.trash');
		}
		
		JHtmlSidebar::setAction('index.php?option=com_mams&view=artauths');
		
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'),'filter_state',JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true));
		
	}
	
	protected function getSortFields()
	{
		return array(
				'a.ordering' => JText::_('JGRID_HEADING_ORDERING')
		);
	}
}
