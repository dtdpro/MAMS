<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelImage extends JModelAdmin
{
	protected function canDelete($record)
	{
		if (!empty($record->img_id))
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
	
	public function getTable($type = 'Image', $prefix = 'MAMSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_mams.image', 'image', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}

	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mams.edit.image.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('image.img_id') == 0) {
				$app = JFactory::getApplication();
				$data->set('img_extension', JRequest::getString('img_extension', $app->getUserState('com_mams.images.filter.extension')));
				
			}
		}
		return $data;
	}	
}
