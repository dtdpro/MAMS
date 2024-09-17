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
		$this->typesl[3] = JHTML::_('select.option',  'seclist','Section Article List');
		$this->typesl[4] = JHTML::_('select.option',  'catlist','Category Article List');
        $this->typesl[5] = JHTML::_('select.option',  'taglist','Tag Article List');
		$this->typesl[6] = JHTML::_('select.option',  'autlist','Author Article List');
		$this->typesl[7] = JHTML::_('select.option',  'authors','Authors List');
		$this->typesl[8] = JHTML::_('select.option',  'dload','Download');
		$this->typesl[9] = JHTML::_('select.option',  'media','Media');
        $this->typesl[10] = JHTML::_('select.option',  'listcats','List of Categories');
        $this->typesl[11] = JHTML::_('select.option',  'listsecs','List of Sections');
        $this->typesl[12] = JHTML::_('select.option',  'listarts','List of All Articles');
        $this->typesl[13] = JHTML::_('select.option',  'link','Link');
		
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
