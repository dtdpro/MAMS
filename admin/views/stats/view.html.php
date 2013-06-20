<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class MAMSViewStats extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->config=MAMSHelper::getConfig();
		
		$typesl[1] = JHTML::_('select.option',  'article','Article Page');
		$typesl[2] = JHTML::_('select.option',  'author','Author Page');
		$typesl[3] = JHTML::_('select.option',  'seclist','Section Artice List');
		$typesl[4] = JHTML::_('select.option',  'catlist','Category Artice List');
		$typesl[5] = JHTML::_('select.option',  'autlist','Author Artice List');
		$typesl[6] = JHTML::_('select.option',  'authors','Authors List');
		$typesl[7] = JHTML::_('select.option',  'dload','Download');
		
		$this->model = $this->getModel();
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		
		//Tool Bar
		JToolBarHelper::title(   JText::_( 'MAMS Stats' ), 'mams' );
		$tbar =& JToolBar::getInstance('toolbar');
		$tbar->appendButton('Link','archive','Export CSV','index.php?option=com_mams&view=stats&format=csv');
		
		//Sidebar Filters
		JHtmlSidebar::setAction('index.php?option=com_mams&view=articles');
		JHtmlSidebar::addFilter(JText::_('Item Type'),'filter_type',JHtml::_('select.options', $typesl, 'value', 'text', $this->model->getState('filter_type')));
		if ($this->config->mue) {
			JHtmlSidebar::addFilter(JText::_('User Group'),'filter_group',JHtml::_('select.options', $this->get('UserGroups'), 'value', 'text', $this->model->getState('filter.group')));
		}
		
		//Sidebar Menu
		MAMSHelper::addSubmenu(JRequest::getVar('view'),JRequest::getCmd('extension', 'com_mams'));
		$this->sidebar = JHtmlSidebar::render();	
		
		parent::display($tpl);
	}
}
