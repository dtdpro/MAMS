<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

class MAMSControllerDloads extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_DLOAD";
	
	public function getModel($name = 'Dload', $prefix = 'MAMSModel', $config = [])
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}