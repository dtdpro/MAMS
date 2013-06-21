<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewArticles extends JViewLegacy
{
	
	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null) 
	{
		$this->state		= $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		
		// Set the submenu
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
		JToolBarHelper::title(JText::_('COM_MAMS_MANAGER_ARTICLES'), 'mams');
		JToolBarHelper::addNew('article.add', 'JTOOLBAR_NEW');
		JToolBarHelper::editList('article.edit', 'JTOOLBAR_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('articles.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('articles.unpublish', 'unpublish.png', 'unpublish_f2.png','JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::custom('articles.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
		JToolBarHelper::custom('articles.unfeatured', 'remove.png', 'remove_f2.png', 'COM_MAMS_TOOLBAR_DEFEATURE', true);
		JToolBarHelper::divider();
		if ($state->get('filter.state') == -2) {
			JToolBarHelper::deleteList('', 'articles.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else  {
			JToolBarHelper::trash('articles.trash');
		}
		JHtml::_('bootstrap.modal', 'collapseModal');
		$title = JText::_('JTOOLBAR_BATCH');
		$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\"><i class=\"icon-checkbox-partial\" title=\"$title\"></i>$title</button>";
		$bar->appendButton('Custom', $dhtml, 'batch');

		
		JHtmlSidebar::setAction('index.php?option=com_mams&view=articles');
		
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'),'filter_state',JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true));
		JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_ACCESS'),'filter_access',JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access')));
		JHtmlSidebar::addFilter(JText::_('COM_MAMS_SELECT_SEC'),'filter_sec',JHtml::_('select.options', MAMSHelper::getSections("article"), 'value', 'text', $this->state->get('filter.sec')));
		
	}
	
	protected function getSortFields()
	{
		return array(
				'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
				'a.published' => JText::_('JSTATUS'),
				'a.art_published' => JText::_('COM_MAMS_ARTICLE_HEADING_PUBLISHED'),
				'a.art_added' => JText::_('COM_MAMS_ARTICLE_HEADING_ADDED'),
				'a.art_modified' => JText::_('COM_MAMS_ARTICLE_HEADING_MODIFIED'),
				'a.art_title' => JText::_('JGLOBAL_TITLE'),
				'a.access' => JText::_('JGRID_HEADING_ACCESS'),
				'a.art_hits' => JText::_('JGLOBAL_HITS'),
				'a.art_id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
