<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelMedia extends JModelAdmin
{
	protected function allowEdit($data = array(), $key = 'med_id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_mams.media.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
	
	public function getScript() 
	{
		return 'administrator/components/com_mams/models/forms/media.js';
	}

	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mams.edit.media.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('media.med_id') == 0) {
				$app = JFactory::getApplication();
				$data->set('med_extension', JRequest::getString('med_extension', $app->getUserState('com_mams.medias.filter.extension')));
				
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
			
	
		$query	= $db->getQuery(true);
		$query->delete();
		$query->from('#__mams_mediafeat');
		$query->where('mf_media IN ('.implode(",",$pks).")");
		$db->setQuery((string)$query);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		if ($feat) 	{
			foreach ($pks as $i => $pk) {
				$qf = 'INSERT INTO #__mams_mediafeat (mf_media) VALUES ('.$pk.')';
				$db->setQuery($qf);
				if (!$db->query()) {
					$this->setError($db->getErrorMsg());
					return false;
				}
			}
		}
			
	
	
		// Clear the component's cache
		$this->cleanCache();
	
		return true;
	}
	
}
