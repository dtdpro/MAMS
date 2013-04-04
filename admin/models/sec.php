<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelSec extends JModelAdmin
{
	protected function allowEdit($data = array(), $key = 'sec_id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_mams.sec.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}
	
	public function getTable($type = 'Sec', $prefix = 'MAMSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_mams.sec', 'sec', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	
	public function getScript() 
	{
		return 'administrator/components/com_mams/models/forms/sec.js';
	}
	
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mams.edit.sec.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('sec.sec_id') == 0) {
				
			}
		}
		return $data;
	}
	
	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();
	
		if (empty($table->am_id)) {
			// Set the values
				
			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__mams_articles WHERE sec_type = "'.$table->sec_type.'"');
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
		$condition[] = 'sec_type = "'.$table->sec_type.'"';
		return $condition;
	}
	
}
