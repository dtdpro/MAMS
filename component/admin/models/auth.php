<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelAuth extends JModelAdmin
{
	
	public $typeAlias = 'com_mams.sec';
		
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
		
		//Tags
		if (!empty($item->auth_id))
		{
			$item->tags = new JHelperTags;
			$item->tags->getTagIds($item->auth_id, 'com_mams.auth');
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

        if ($commands['featsection_id'] != 0)
        {
            if (!$this->batchSection($commands['featsection_id'], $pks, $contexts))
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

     protected function batchSection($value, $pks, $contexts)
    {
        // Set the variables
        $user = JFactory::getUser();
        $table = $this->getTable();

        foreach ($pks as $pk) {
            if ($user->authorise('core.edit', $contexts[$pk]))	{
                $table->reset();
                $table->load($pk);
                $table->auth_sec = (int) $value;

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
