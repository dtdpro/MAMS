<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelFieldgroup extends JModelAdmin
{
	protected function canDelete($record)
	{
		if ($record->group_id == 1) return false;
		
		if (!empty($record->group_id))
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
		if ($record->group_id == 1) return false;
		
		$user = JFactory::getUser();
	
		return parent::canEditState($record);
	}
	
	public function getTable($type = 'Fieldgroup', $prefix = 'MAMSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_mams.fieldgroup', 'fieldgroup', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mams.edit.fieldgroup.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('fieldgroup.group_id') == 0) {
				
			}
		}
		return $data;
	}
	
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		parent::preprocessForm($form,$data,$group);
	
		//Disallow editing of primary fields
		if ($data->group_id == 1) {
			$form->setFieldAttribute('group_title', 'disabled', 'true');
			$form->setFieldAttribute('group_name', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');
			$form->setFieldAttribute('access', 'disabled', 'true');
	
			$form->setFieldAttribute('group_title', 'filter', 'unset');
			$form->setFieldAttribute('group_name', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
			$form->setFieldAttribute('access', 'filter', 'unset');
		}
	}
	
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();
	
		if (empty($table->group_id)) {
			// Set the values
				
			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__mams_article_fieldgroups');
				$max = $db->loadResult();
	
				$table->ordering = $max+1;
			}
		}
		else {
			// Set the values
		}
	}
	
	public function reorder($pks, $delta = 0)
	{
		$table = $this->getTable();
		$pks = (array) $pks;
		$result = true;
	
		$allowed = true;
	
		foreach ($pks as $i => $pk)
		{
			$table->reset();
	
			if ($table->load($pk) && $this->checkout($pk))
			{
				$where = array();
				$where = $this->getReorderConditions($table);
	
				if (!$table->move($delta, $where))
				{
					$this->setError($table->getError());
					unset($pks[$i]);
					$result = false;
				}
	
				$this->checkin($pk);
			}
			else
			{
				$this->setError($table->getError());
				unset($pks[$i]);
				$result = false;
			}
		}
	
		if ($allowed === false && empty($pks))
		{
			$result = null;
		}
	
		// Clear the component's cache
		if ($result == true)
		{
			$this->cleanCache();
		}
	
		return $result;
	}
	
	public function saveorder($pks = null, $order = null)
	{
		$table = $this->getTable();
		$conditions = array();
	
		if (empty($pks))
		{
			return JError::raiseWarning(500, JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
		}
	
		// Update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);
	
			// Access checks.
			if ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];
	
				if (!$table->store())
				{
					$this->setError($table->getError());
					return false;
				}
	
				// Remember to reorder within position and client_id
				$condition = $this->getReorderConditions($table);
				$found = false;
	
				foreach ($conditions as $cond)
				{
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}
	
				if (!$found)
				{
					$key = $table->getKeyName();
					$conditions[] = array($table->$key, $condition);
				}
			}
		}
		
		// Execute reorder for each category.
		foreach ($conditions as $cond)
		{
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}
		
		// Clear the component's cache
		$this->cleanCache();
		
		return true;
	}
}
