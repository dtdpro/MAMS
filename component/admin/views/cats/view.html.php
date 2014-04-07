<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewCats extends JViewLegacy
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
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::title(JText::_('COM_MAMS_MANAGER_CATS'), 'mams');
		JToolBarHelper::addNew('cat.add', 'JTOOLBAR_NEW');
		JToolBarHelper::editList('cat.edit', 'JTOOLBAR_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('cats.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('cats.unpublish', 'unpublish.png', 'unpublish_f2.png','JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::divider();
		JToolBarHelper::custom('cats.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
		JToolBarHelper::custom('cats.unfeatured', 'remove.png', 'remove_f2.png', 'COM_MAMS_TOOLBAR_DEFEATURE', true);
		JToolBarHelper::divider();
		if ($state->get('filter.state') == -2) {
			JToolBarHelper::deleteList('', 'cats.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else  {
			JToolBarHelper::trash('cats.trash');
		}
		
		//Batch Button
		JHtml::_('bootstrap.modal', 'collapseModal');
		$title = JText::_('JTOOLBAR_BATCH');
		
		// Instantiate a new JLayoutFile instance and render the batch button
		$layout = new JLayoutFile('joomla.toolbar.batch');
		
		$dhtml = $layout->render(array('title' => $title));
		$bar->appendButton('Custom', $dhtml, 'batch');
		
		JHtmlSidebar::setAction('index.php?option=com_mams&view=cats');
		
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'),'filter_state',JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true));
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_ACCESS'),'filter_access',JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access')));
	}
	
	protected function getSortFields()
	{
		return array(
				'c.published' => JText::_('JSTATUS'),
				'c.cat_title' => JText::_('COM_MAMS_SEC_HEADING_NAME'),
				'c.access' => JText::_('JGRID_HEADING_ACCESS'),
				'c.cat_id' => JText::_('JGRID_HEADING_ID'),
				'c.cat_added' => JText::_('COM_MAMS_SEC_ADDED'),
				'c.cat_modified' => JText::_('COM_MAMS_SEC_MODIFIED')
		);
	}
}