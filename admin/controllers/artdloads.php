<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

class MAMSControllerArtDloads extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_ARTDLOAD";
	
	public function getModel($name = 'ArtDload', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}