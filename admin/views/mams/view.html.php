<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class MAMSViewMAMS extends JViewLegacy
{
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'MAMS Article Management System' ), 'mams' );
		JToolBarHelper::preferences('com_mams');
		parent::display($tpl);
	}
}
