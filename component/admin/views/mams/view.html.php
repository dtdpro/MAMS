<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class MAMSViewMAMS extends JViewLegacy
{
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'MAMS Article Management System' ), 'mams' );
		JToolBarHelper::preferences('com_mams');
		// Set the submenu
		MAMSHelper::addSubmenu('mams',JRequest::getCmd('extension', 'com_mams'));
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}
}
