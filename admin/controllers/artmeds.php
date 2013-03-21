<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

class MAMSControllerArtMeds extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_ARTMED";
	
	public function getModel($name = 'ArtMed', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}