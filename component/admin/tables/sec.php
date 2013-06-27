<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableSec extends JTable
{
	protected $tagsHelper = null;
	
	function __construct(&$db) 
	{
		parent::__construct('#__mams_secs', 'sec_id', $db);

		$this->tagsHelper = new JHelperTags();
		$this->tagsHelper->typeAlias = 'com_mams.sec';
	}
	
	public function bind($array, $ignore = '')
	{
		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}
	
		return parent::bind($array, $ignore);
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->sec_id) {
			// Existing item
			$this->sec_modified		= $date->toSql();
		} else {
			// New section. A section created on field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->sec_cadded)) {
				$this->sec_added = $date->toSql();
				$this->sec_modified		= $date->toSql();
			}
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('Sec', 'MAMSTable');
		if ($table->load(array('sec_alias'=>$this->sec_alias)) && ($table->sec_id != $this->sec_id || $this->sec_id==0)) {
			$this->setError(JText::_('COM_MAMS_ERROR_UNIQUE_ALIAS'));
			return false;
		}
		// Attempt to store the user data.	
		$this->tagsHelper->preStoreProcess($this);
		$result = parent::store($updateNulls);
		return $result && $this->tagsHelper->postStoreProcess($this);
	}
	
	public function check()
	{
		// check for valid name
		if (trim($this->sec_name) == '') {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_TITLE'));
			return false;
		}

		// check for existing name
		$query = 'SELECT sec_id FROM #__mams_secs WHERE sec_name = '.$this->_db->Quote($this->sec_name);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->sec_id)) {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_NAME'));
			return false;
		}

		if (empty($this->sec_alias)) {
			$this->sec_alias = $this->sec_name;
		}
		$this->sec_alias = JApplication::stringURLSafe($this->sec_alias);
		if (trim(str_replace('-','',$this->sec_alias)) == '') {
			$this->sec_alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		return true;
	}
	
	public function delete($pk = null)
	{
		$result = parent::delete($pk);
		return $result && $this->tagsHelper->deleteTagData($this, $pk);
	}
	
}