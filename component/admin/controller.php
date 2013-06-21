<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class MAMSController extends JControllerLegacy
{
	protected $default_view = 'mams';
	
	function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/mams.php';
		
		parent::display();
		return $this;
	}
}
