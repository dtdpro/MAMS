<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

class MAMSControllerLinks extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_LINK";
	
	public function getModel($name = 'Link', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}