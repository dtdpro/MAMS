<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewTags extends JViewLegacy
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
		JToolBarHelper::title(JText::_('COM_MAMS_MANAGER_TAGS'), 'mams');
		JToolBarHelper::addNew('tag.add', 'JTOOLBAR_NEW');
		JToolBarHelper::editList('tag.edit', 'JTOOLBAR_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('tags.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('tags.unpublish', 'unpublish.png', 'unpublish_f2.png','JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::divider();
		JToolBarHelper::custom('tags.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
		JToolBarHelper::custom('tags.unfeatured', 'remove.png', 'remove_f2.png', 'COM_MAMS_TOOLBAR_DEFEATURE', true);
		JToolBarHelper::divider();
		if ($state->get('filter.state') == -2) {
			JToolBarHelper::deleteList('', 'tags.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else  {
			JToolBarHelper::trash('tags.trash');
		}
		
		//Batch Button
		$title = JText::_('JTOOLBAR_BATCH');
		
		// Instantiate a new JLayoutFile instance and render the batch button
		$layout = new JLayoutFile('joomla.toolbar.batch');
		
		$dhtml = $layout->render(array('title' => $title));
		$bar->appendButton('Custom', $dhtml, 'batch');
	}
	
	protected function getSortFields()
	{
		return array(
				'c.published' => JText::_('JSTATUS'),
				'c.tag_title' => JText::_('COM_MAMS_SEC_HEADING_NAME'),
				'c.access' => JText::_('JGRID_HEADING_ACCESS'),
				'c.tag_id' => JText::_('JGRID_HEADING_ID'),
				'c.tag_added' => JText::_('COM_MAMS_SEC_ADDED'),
				'c.tag_modified' => JText::_('COM_MAMS_SEC_MODIFIED'),
                'tag_items' => JText::_('COM_MAMS_CAT_HEADING_NUMITEMS')
		);
	}
}