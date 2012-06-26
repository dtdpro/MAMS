<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class MAMSController extends JController
{
	protected $default_view = 'mams';
	
	function display()
	{
		require_once JPATH_COMPONENT.'/helpers/mams.php';
		// Set the submenu
		MAMSHelper::addSubmenu(JRequest::getVar('view'),JRequest::getCmd('extension', 'com_mams'));
		parent::display();
		return $this;
	}
}
