<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;


// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');


class MAMSControllerFieldgroups extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_FIELDGROUP";
	
	public function getModel($name = 'FieldGroup', $prefix = 'MAMSModel', $config = [])
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	public function saveOrderAjax()
	{
		// Get the input
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');
	
		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);
	
		// Get the model
		$model = $this->getModel();
	
		// Save the ordering
		$return = $model->saveorder($pks, $order);
	
		if ($return)
		{
			echo "1";
		}
	
		// Close the application
		JFactory::getApplication()->close();
	}
}