<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\String\StringHelper;

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelSec extends JModelAdmin
{
	
	public $typeAlias = 'com_mams.sec';
		
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
		if (!empty($record->sec_id))
		{
			return $user->authorise('core.edit.state', 'com_mams.sec.' . (int) $record->sec_id);
		} else { 
			return parent::canEditState("com_mams");
		}
	}
	
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
	
		//Tags
		if (!empty($item->sec_id))
		{
			$item->tags = new JHelperTags;
			$item->tags->getTagIds($item->sec_id, 'com_mams.sec');
		}
	
		return $item;
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
	
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'sec_type = "'.$table->sec_type.'"';
		return $condition;
	}

	public function save($data)
	{
		$table      = $this->getTable();
		$input      = JFactory::getApplication()->input;
		$pk         = (!empty($data['sec_id'])) ? $data['sec_id'] : (int) $this->getState($this->getName() . '.id');
		$isNew      = true;
		$context    = $this->option . '.' . $this->name;

		// Load the row if saving an existing category.
		if ($pk > 0)
		{
			$table->load($pk);
			$isNew = false;
		} else {
			$data['level']=0;
			$data['lft']=0;
			$data['rgt']=0;
			$data['path']="";
			$data['metadata']="";
			$data['asset_id']=0;
		}

		// Set the new parent id if parent id not matched OR while New/Save as Copy .
		if ($table->parent_id != $data['parent_id'] || $data['sec_id'] == 0)
		{
			$table->setLocation($data['parent_id'], 'last-child');
		}

		// Alter the title for save as copy
		if ($input->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('sec_id'));

			if ($data['sec_name'] == $origTable->title)
			{
				list($title, $alias) = $this->generateNewTitle($data['parent_id'], $data['sec_alias'], $data['sec_name']);
				$data['sec_name'] = $title;
				$data['sec_alias'] = $alias;
			}
			else
			{
				if ($data['sec_alias'] == $origTable->alias)
				{
					$data['sec_alias'] = '';
				}
			}

			$data['published'] = 0;
		}

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());

			return false;
		}

		// Bind the rules.
		if (isset($data['rules']))
		{
			$rules = new JAccessRules($data['rules']);
			$table->setRules($rules);
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());

			return false;
		}

		// Rebuild the path for the section:
		if (!$table->rebuildPath($table->sec_id))
		{
			$this->setError($table->getError());

			return false;
		}

		if (!$table->rebuild())
		{
			$this->setError($table->getError());

			return false;
		}

		$this->setState($this->getName() . '.id', $table->sec_id);

		return true;
	}

	public function rebuild()
	{
		// Get an instance of the table object.
		$table = $this->getTable();

		if (!$table->rebuild())
		{
			$this->setError($table->getError());

			return false;
		}

		return true;
	}

	protected function generateNewTitle($parent_id, $alias, $title)
	{
		// Alter the title & alias
		$table = $this->getTable();
		while ($table->load(array('sec_alias' => $alias, 'parent_id' => $parent_id)))
		{
			$title = StringHelper::increment($title);
			$alias = StringHelper::increment($alias, 'dash');
		}
		return array($title, $alias);
	}

	public function saveorder($idArray = null, $lft_array = null)
	{
		// Get an instance of the table object.
		$table = $this->getTable();
		if (!$table->saveorder($idArray, $lft_array))
		{
			$this->setError($table->getError());
			return false;
		}
		// Clear the cache
		$this->cleanCache();
		return true;
	}

}
