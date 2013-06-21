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
		echo '<div id="system">';
		parent::display();
		echo '</div>';
	}

}
?>
