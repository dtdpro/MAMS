<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelDload extends JModelAdmin
{
	
	protected function canDelete($record)
	{
		if (!empty($record->dl_id))
		{
			if ($record->published != -2)
			{
				return;
			}
			$user = JFactory::getUser();
	
			return parent::canDelete($record);
		}
	}
	
	protected function canEditState($record)
	{
		$user = JFactory::getUser();
	
		return parent::canEditState($record);
	}
	
	public function getTable($type = 'Dload', $prefix = 'MAMSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_mams.dload', 'dload', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mams.edit.dload.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('dload.dl_id') == 0) {
				$app = JFactory::getApplication();
				$data->set('dl_extension', JRequest::getString('dl_extension', $app->getUserState('com_mams.dloads.filter.extension')));
				
			}
		}
		return $data;
	}
	
}
