<?php

jimport('joomla.application.component.controller');


class MAMSController extends JControllerLegacy
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		$vName = $this->input->getCmd('view', 'artlist');
		$this->input->set('view', $vName);
		parent::display();
	}

}
?>
