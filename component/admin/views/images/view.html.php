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
		$bar = JToolBar::getInstance('toolbar');
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

		$title = JText::_('JTOOLBAR_BATCH');
		$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\"><i class=\"icon-checkbox-partial\" title=\"$title\"></i>$title</button>";
		$bar->appendButton('Custom', $dhtml, 'batch');
	}
	
	protected function getSortFields()
	{
		return array(
				'i.ordering' => JText::_('JGRID_HEADING_ORDERING'),
				'i.img_inttitle' => JText::_('COM_MAMS_IMAGE_HEADING_NAME'),
				'i.access' => JText::_('JGRID_HEADING_ACCESS'),
				'i.img_added' => JText::_('COM_MAMS_IMAGE_ADDED'),
				'i.img_id' => JText::_('JGRID_HEADING_ID'),
				'i.img_modified' => JText::_('COM_MAMS_IMAGE_MODIFIED')
		);
	}
}
