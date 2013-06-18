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
		MAMSHelper::addSubmenu(JRequest::getVar('view'),JRequest::getCmd('extension', 'com_mams'));
		parent::display($tpl);
	}
}
