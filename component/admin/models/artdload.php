<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelArtDload extends JModelAdmin
{
	protected function canDelete($record)
	{
		if (!empty($record->aa_id))
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
	
	public function getTable($type = 'ArtDload', $prefix = 'MAMSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_mams.artdload', 'artdload', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mams.edit.artdload.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('artdload.ad_id') == 0) {
				$app = JFactory::getApplication();
				$data->set('ad_art', JRequest::getInt('ad_art', $app->getUserState('com_mams.drilldowns.filter.article')));
				$data->set('ad_field', JRequest::getInt('ad_field', $app->getUserState('com_mams.artdloads.filter.field')));
			}
		}
		return $data;
	}
	
	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		
		if (empty($table->ad_id)) {
			// Set the values
			
			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__mams_artdl WHERE ad_field = "'.$table->ad_field.'" && ad_art = "'.$table->ad_art.'"');
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
		$condition[] = 'ad_field = "'.$table->ad_field.'" && ad_art = '.(int) $table->ad_art;
		return $condition;
	}
	
}
