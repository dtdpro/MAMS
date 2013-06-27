<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelAuth extends JModelAdmin
{
	protected function canDelete($record)
	{
		if (!empty($record->auth_id))
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
	
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$table = $this->getTable();
	
		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);
	
			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}
	
		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = JArrayHelper::toObject($properties, 'JObject');
		
		// Convert the metadata field to an array.
		$registry = new JRegistry;
		$registry->loadString($item->metadata);
		$item->metadata = $registry->toArray();
	
		//Tags
		if (!empty($item->auth_id))
		{
			$item->tags = new JHelperTags;
			$item->tags->getTagIds($item->auth_id, 'com_mams.auth');
			$item->metadata['tags'] = $item->tags;
		}
	
		return $item;
	}
	
	public function getTable($type = 'Auth', $prefix = 'MAMSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_mams.auth', 'auth', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mams.edit.auth.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('auth.auth_id') == 0) {
				
			}
		}
		return $data;
	}
	
	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();
	
		if (empty($table->auth_id)) {
			// Set the values
				
			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__mams_authors WHERE auth_sec = '.$table->auth_sec);
				$max = $db->loadResult();
	
				$table->ordering = $max+1;
			}
		}
		else {
			// Set the values
		}
	}
	
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'auth_sec = '.(int) $table->auth_sec;
		return $condition;
	}
	
}
