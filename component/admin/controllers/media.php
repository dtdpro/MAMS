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
	}
}
