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

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		$state	= $this->get('State');
        $bar = JToolBar::getInstance('toolbar');
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
        //Batch Button
        $title = JText::_('JTOOLBAR_BATCH');
        $layout = new JLayoutFile('joomla.toolbar.batch');
        $dhtml = $layout->render(array('title' => $title));
        $bar->appendButton('Custom', $dhtml, 'batch');
	}
	
	protected function getSortFields()
	{
		return array(
				'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
				'a.published' => JText::_('JSTATUS'),
				'a.auth_added' => JText::_('COM_MAMS_AUTH_ADDED'),
				'a.auth_modified' => JText::_('COM_MAMS_AUTH_MODIFIED'),
				'a.auth_fname' => JText::_('COM_MAMS_AUTH_HEADING_FNAME'),
				'a.auth_lname' => JText::_('COM_MAMS_AUTH_HEADING_LNAME'),
				'a.access' => JText::_('JGRID_HEADING_ACCESS'),
				'a.auth_id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
