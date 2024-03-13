<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class MAMSControllerDload extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_DLOAD";
	
	protected $extension;

	public function __construct($config = array())
	{
		parent::__construct($config);
	}
}
