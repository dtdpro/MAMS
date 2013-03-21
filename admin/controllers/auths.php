<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

class MAMSControllerAuths extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_AUTH";
	
	public function getModel($name = 'Auth', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}