<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelArtMed extends JModelAdmin
{
	protected function canDelete($record)
	{
		if (!empty($record->am_id))
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
	
	public function getTable($type = 'ArtMed', $prefix = 'MAMSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_mams.artmed', 'artmed', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mams.edit.artmed.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('artmed.am_id') == 0) {
				$app = JFactory::getApplication();
				$data->set('am_art', JRequest::getInt('am_art', $app->getUserState('com_mams.drilldowns.filter.article')));
				$data->set('am_field', JRequest::getInt('am_field', $app->getUserState('com_mams.artmeds.filter.field')));
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
				$db->setQuery('SELECT MAX(ordering) FROM #__mams_artmed WHERE am_field = "'.$table->am_field.'" && am_art = "'.$table->am_art.'"');
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
		$condition[] = 'am_field = "'.$table->am_field.'" && am_art = '.(int) $table->am_art;
		return $condition;
	}
	
}
