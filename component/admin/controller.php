<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class MAMSController extends JControllerLegacy
{
	function display($cachable = false, $urlparams = false)
	{
		$jinput = JFactory::getApplication()->input;

		// set default view if not set
		$jinput->set('view', $jinput->getCmd('view', 'articles'));

		// call parent behavior
		parent::display($cachable,$urlparams);

	}
}
