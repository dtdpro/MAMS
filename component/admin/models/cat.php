<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelCat extends JModelAdmin
{
	protected function canDelete($record)
	{
		if (!empty($record->cat_id))
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
	
	public function getTable($type = 'Cat', $prefix = 'MAMSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_mams.cat', 'cat', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mams.edit.cat.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('cat.cat_id') == 0) {
				
			}
		}
		return $data;
	}
	
	public function featured(&$pks,$value=0)
	{
		$table = $this->getTable();
		$pks = (array) $pks;

		// Include the content plugins for the change of state event.
		JPluginHelper::importPlugin('content');

		foreach ($pks as $pk)
		{
			$table->reset();
			$table->load($pk);
			$table->cat_featured = (int) $value;

			if (!$table->store())
			{
				$this->setError($table->getError());

				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
	
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize user ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);
	
		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}
	
		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}
	
		$done = false;
	
		if (!empty($commands['assetgroup_id']))
		{
			if (!$this->batchAccess($commands['assetgroup_id'], $pks, $contexts))
			{
				return false;
			}
	
			$done = true;
		}
	
		if (!empty($commands['featassetgroup_id']))
		{
			if (!$this->batchFeatAccess($commands['featassetgroup_id'], $pks, $contexts))
			{
				return false;
			}
	
			$done = true;
		}
	
		if (!$done)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}
	
		// Clear the cache
		$this->cleanCache();
	
		return true;
	}
	
	protected function batchFeatAccess($value, $pks, $contexts)
	{
		// Set the variables
		$user = JFactory::getUser();
		$table = $this->getTable();
	
		foreach ($pks as $pk) {
			if ($user->authorise('core.edit', $contexts[$pk])) {
				$table->reset();
				$table->load($pk);
				$table->cat_feataccess = (int) $value;
	
				if (!$table->check()) { $this->setError($table->getError()); return false; }
	
				if (!$table->store()) { $this->setError($table->getError()); return false; }
			} else {
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}
		}
	
		// Clean the cache
		$this->cleanCache();
	
		return true;
	}
	
	
	
}
