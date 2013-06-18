<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class MAMSViewStats extends JViewLegacy
{
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'MAMS Stats' ), 'mams' );
		// Set the submenu
		MAMSHelper::addSubmenu(JRequest::getVar('view'),JRequest::getCmd('extension', 'com_mams'));
		$model = $this->getModel();
		$tbar =& JToolBar::getInstance('toolbar');
		$tbar->appendButton('Link','archive','Export CSV','index.php?option=com_mams&view=stats&format=csv" target="_blank');
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->startdate = $model->getState('startdate');
		$this->enddate = $model->getState('enddate');
		$this->filter_type = $model->getState('filter_type');
		$this->filter_group = $model->getState('filter.group');
		
		$this->config=MAMSHelper::getConfig();
		if ($this->config->continued || $this->config->mue) {
			$this->grouplist=$this->get('UserGroups');
		}
		
		parent::display($tpl);
	}
}
