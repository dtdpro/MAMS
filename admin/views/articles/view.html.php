<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class MAMSViewArticles extends JView
{
	
	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null) 
	{
		// Get data from the model
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state		= $this->get('State');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	protected function addToolBar() 
	{
		$state	= $this->get('State');
		JToolBarHelper::title(JText::_('COM_MAMS_MANAGER_ARTICLES'), 'mams');
		JToolBarHelper::addNew('article.add', 'JTOOLBAR_NEW');
		JToolBarHelper::editList('article.edit', 'JTOOLBAR_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::custom('articles.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('articles.unpublish', 'unpublish.png', 'unpublish_f2.png','JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::custom('articles.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
		JToolBarHelper::divider();
		if ($state->get('filter.published') == -2) {
			JToolBarHelper::deleteList('', 'articles.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} else  {
			JToolBarHelper::trash('articles.trash');
		}
	}
	
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_MAMS_MANAGER_ARTICLES'));
	}
}
