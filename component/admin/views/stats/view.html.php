<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class MAMSViewStats extends JViewLegacy
{
	function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;
		$this->config=MAMSHelper::getConfig();
		
		$this->typesl[1] = JHTML::_('select.option',  'article','Article Page');
		$this->typesl[2] = JHTML::_('select.option',  'author','Author Page');
		$this->typesl[3] = JHTML::_('select.option',  'seclist','Section Artice List');
		$this->typesl[4] = JHTML::_('select.option',  'catlist','Category Artice List');
		$this->typesl[5] = JHTML::_('select.option',  'autlist','Author Artice List');
		$this->typesl[6] = JHTML::_('select.option',  'authors','Authors List');
		$this->typesl[7] = JHTML::_('select.option',  'dload','Download');
		$this->typesl[8] = JHTML::_('select.option',  'media','Media');
		
		$this->model = $this->getModel();
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		
		//Tool Bar
		JToolBarHelper::title(   JText::_( 'MAMS Stats' ), 'mams' );
		$tbar = JToolBar::getInstance('toolbar');
		$tbar->appendButton('Link','archive','Export CSV','index.php?option=com_mams&view=stats&format=csv');
		
		//Sidebar Menu
		if (JVersion::MAJOR_VERSION == 3)  MAMSHelper::addSubmenu($jinput->getVar('view'),$jinput->getVar('extension', 'com_mams'));
		$this->sidebar = JHtmlSidebar::render();	
		
		parent::display($tpl);
	}
}
