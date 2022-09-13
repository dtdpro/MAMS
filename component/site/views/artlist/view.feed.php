<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class MAMSViewArtList extends JViewLegacy
{
	public function display($tpl = null)
	{
		JError::raiseError(404,"Feed no longer available");
		$this->error=true;
	}
}
?>
