<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class MAMSControllerImage extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_IMAGE";
	
	protected $extension;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
	
		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->extension))
		{
			$this->extension = JFactory::getApplication()->input->get('extension', 'com_mams');
		}
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'img_id')
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
	
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		// Set the model
		$model = $this->getModel('Image', '', array());
	
		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_mams&view=images' . $this->getRedirectToListAppend(), false));
	
		return parent::batch($model);
	}
}
