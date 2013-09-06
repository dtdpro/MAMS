<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableFieldgroup extends JTable
{
	function __construct(&$db) 
	{
		parent::__construct('#__mams_article_fieldgroups', 'group_id', $db);
	}

	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}
	
		return parent::bind($array, $ignore);
	}
	public function store($updateNulls = false)
	{
		// Verify that the alias is unique
		$table = JTable::getInstance('FieldGroup', 'MAMSTable');
		if ($table->load(array('group_name'=>$this->group_name)) && ($table->group_id != $this->group_id || $this->group_id==0)) {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_NAME'));
			return false;
		}
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
	
	public function check()
	{
		// check for valid name
		if (trim($this->group_title) == '') {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_TITLE'));
			return false;
		}

		if (empty($this->group_name)) {
			$this->group_name = $this->group_title;
		}
		$this->group_name = JApplication::stringURLSafe($this->group_name);
		if (trim(str_replace('-','',$this->group_name)) == '') {
			$this->group_name = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		return true;
	}
}