<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class MAMSControllerSec extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_SEC";
	
	protected function allowEdit($data = array(), $key = 'sec_id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();
		$userId = $user->get('id');
	
		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_mams.sec.' . $recordId))
		{
			return true;
		}
	
		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}
}
