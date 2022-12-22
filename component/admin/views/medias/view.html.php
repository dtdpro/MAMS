<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewMedias extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null) 
	{
		$jinput = JFactory::getApplication()->input;

		$this->state		= $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		if (JVersion::MAJOR_VERSION == 3)  MAMSHelper::addSubmenu($jinput->getVar('view'),$jinput->getVar('extension', 'com_mams'));

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
		JToolBarHelper::title(JText::_('COM_MAMS_MANAGER_MEDIAS'), 'mams');
		JToolBarHelper::addNew('media.add', 'JTOOLBAR_NEW');
		JToolBarHelper::editList('media.edit', 'JTOOLBAR_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('medias.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('medias.unpublish', 'unpublish.png', 'unpublish_f2.png','JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::custom('medias.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
		JToolBarHelper::custom('medias.defeatured', 'remove.png', 'remove_f2.png', 'COM_MAMS_TOOLBAR_DEFEATURE', true);
		JToolBarHelper::divider();
		if ($state->get('filter.state') == -2) {
			JToolBarHelper::deleteList('', 'medias.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else  {
			JToolBarHelper::trash('medias.trash');
		}
	}
	
	protected function getSortFields()
	{
		return array(
				'm.med_inttitle' => JText::_('COM_MAMS_MEDIA_HEADING_NAME'),
				'm.access' => JText::_('JGRID_HEADING_ACCESS'),
				'm.med_added' => JText::_('COM_MAMS_MEDIA_ADDED'),
				'm.med_id' => JText::_('JGRID_HEADING_ID'),
				'm.med_modified' => JText::_('COM_MAMS_MEDIA_MODIFIED')
		);
	}
}
