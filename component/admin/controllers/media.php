<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class MAMSControllerMedia extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_MEDIA";
	
	protected $extension;
	
	public function __construct($config = array())
	{
		$this->view_list = "medias";

		parent::__construct($config);
	
		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->extension))
		{
			$this->extension = JFactory::getApplication()->input->get('extension', 'com_mams');
		}
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'med_id')
	{
		$append = parent::getRedirectToItemAppend($recordId,$urlVar);
		$append .= '&extension=' . $this->extension;
	
		return $append;
	}
	
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&extension=' . $this->extension;
	
		return $append;
	}
}
