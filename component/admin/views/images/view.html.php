<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewImages extends JViewLegacy
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
		JToolBarHelper::title(JText::_('COM_MAMS_MANAGER_IMAGES'), 'mams');
		JToolBarHelper::addNew('image.add', 'JTOOLBAR_NEW');
		JToolBarHelper::editList('image.edit', 'JTOOLBAR_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('images.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('images.unpublish', 'unpublish.png', 'unpublish_f2.png','JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::divider();
		if ($state->get('filter.state') == -2) {
			JToolBarHelper::deleteList('', 'images.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else  {
			JToolBarHelper::trash('images.trash');
		}
		
		JHtmlSidebar::setAction('index.php?option=com_mams&view=images');
		
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'),'filter_state',JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true));
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_ACCESS'),'filter_access',JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access')));
	}
	
	protected function getSortFields()
	{
		return array(
				'i.published' => JText::_('JSTATUS'),
				'i.img_inttitle' => JText::_('COM_MAMS_IMAGE_HEADING_NAME'),
				'i.access' => JText::_('JGRID_HEADING_ACCESS'),
				'i.img_added' => JText::_('COM_MAMS_IMAGE_ADDED'),
				'i.img_id' => JText::_('JGRID_HEADING_ID'),
				'i.img_modified' => JText::_('COM_MAMS_IMAGE_MODIFIED')
		);
	}
}
