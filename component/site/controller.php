<?php

jimport('joomla.application.component.controller');


class MAMSController extends JControllerLegacy
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display($cachable = false, $urlparams = false)
	{
		$vName = $this->input->getCmd('view', 'artlist');
		$this->input->set('view', $vName);
		parent::display($cachable,$urlparams);
	}

}
?>
