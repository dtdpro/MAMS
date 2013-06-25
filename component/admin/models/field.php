<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelField extends JModelAdmin
{
	protected function canDelete($record)
	{
		if (!empty($record->sec_id))
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
	
	public function getTable($type = 'Field', $prefix = 'MAMSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_mams.field', 'field', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}

		return $form;
	}
	
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mams.edit.field.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('fieldgroup.field_id') == 0) {
				
			}
		}
		return $data;
	}
	
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// Import the appropriate plugin group.
		JPluginHelper::importPlugin($group);
	
		// Get the dispatcher.
		$dispatcher = JEventDispatcher::getInstance();
	
		// Trigger the form preparation event.
		$results = $dispatcher->trigger('onContentPrepareForm', array($form, $data));
		
		//Disallow editing of primary fields
		if ($data->field_id < 10 && $data->field_id != 0) {
			$form->setFieldAttribute('field_title', 'disabled', 'true');
			$form->setFieldAttribute('field_name', 'disabled', 'true');
			$form->setFieldAttribute('field_group', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');
			$form->setFieldAttribute('access', 'disabled', 'true');
				
			$form->setFieldAttribute('field_title', 'filter', 'unset');
			$form->setFieldAttribute('field_name', 'filter', 'unset');
			$form->setFieldAttribute('field_group', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
			$form->setFieldAttribute('access', 'filter', 'unset');
		}
	
		// Check for errors encountered while preparing the form.
		if (count($results) && in_array(false, $results, true))
		{
			// Get the last error.
			$error = $dispatcher->getError();
	
			if (!($error instanceof Exception))
			{
				throw new Exception($error);
			}
		}
	}
	
	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();
	
		if (empty($table->group_id)) {
			// Set the values
				
			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__mams_article_fields');
				$max = $db->loadResult();
	
				$table->ordering = $max+1;
			}
		}
		else {
			// Set the values
		}
	}
}
