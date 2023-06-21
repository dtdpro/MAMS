<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelMedia extends JModelAdmin
{
	protected function canDelete($record)
	{
		if (!empty($record->med_id))
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
	
	public function getTable($type = 'Media', $prefix = 'MAMSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_mams.media', 'media', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}

	protected function loadFormData() 
	{
		$jinput = JFactory::getApplication()->input;
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mams.edit.media.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('media.med_id') == 0) {
				$app = JFactory::getApplication();
				$data->set('med_extension', $jinput->get('med_extension', $app->getUserState('com_mams.medias.filter.extension','com_mams')));
				
			}
		}
		return $data;
	}
	
	public function featured(&$pks,$feat)
	{
		// Initialise variables.
		$user = JFactory::getUser();
		$pks = (array) $pks;
		$db	= $this->getDbo();
		$table = $this->getTable('FeaturedMedia', 'MAMSTable');
	
		$query	= $db->getQuery(true);
		$query->delete();
		$query->from('#__mams_mediafeat');
		$query->where('mf_media IN ('.implode(",",$pks).")");
		$db->setQuery((string)$query);
		if (!$db->execute()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		if ($feat) 	{
			foreach ($pks as $i => $pk) {
				$qf = 'INSERT INTO #__mams_mediafeat (mf_media) VALUES ('.$pk.')';
				$db->setQuery($qf);
				if (!$db->execute()) {
					$this->setError($db->getErrorMsg());
					return false;
				}
			}
		}
			
		$table->reorder();
	
		// Clear the component's cache
		$this->cleanCache();
	
		return true;
	}
	
}
