<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableAuth extends JTable
{
	protected $tagsHelper = null;

	function __construct(&$db) 
	{
		parent::__construct('#__mams_authors', 'auth_id', $db);

		$this->tagsHelper = new JHelperTags();
		$this->tagsHelper->typeAlias = 'com_mams.auth';
	}
	
	public function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->auth_id) {
			// Existing item
			$this->auth_modified		= $date->toSql();
		} else {
			// New section. A section created on field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->auth_added)) {
				$this->auth_added = $date->toSql();
				$this->auth_modified		= $date->toSql();
			}
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('Auth', 'MAMSTable');
		if ($table->load(array('auth_alias'=>$this->auth_alias)) && ($table->auth_id != $this->auth_id || $this->auth_id==0)) {
			$this->setError(JText::_('COM_MAMS_ERROR_UNIQUE_ALIAS'));
			return false;
		}
		
		$this->auth_name = trim($this->auth_fname.(($this->auth_mi) ? " ".$this->auth_mi : "")." ".$this->auth_lname.(($this->auth_titles) ? ", ".$this->auth_titles : ""));
		
		// Attempt to store the user data.	
		$this->tagsHelper->preStoreProcess($this);
		$result = parent::store($updateNulls);
		return $result && $this->tagsHelper->postStoreProcess($this);
	}
	
	public function check()
	{
		// check for valid name
		if (trim($this->auth_fname) == '' || trim($this->auth_lname) == '') {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_TITLE'));
			return false;
		}

		// check for existing name
		$query = 'SELECT auth_id FROM #__mams_authors WHERE auth_fname = '.$this->_db->Quote($this->auth_fname).' && auth_lname = '.$this->_db->Quote($this->auth_lname).' && auth_mi = '.$this->_db->Quote($this->auth_mi);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->auth_id) && !$this->auth_mirror) {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_NAME'));
			return false;
		}

		if (empty($this->auth_alias)) {
			$this->auth_alias = $this->auth_fname.'-'.$this->auth_lname;
		}
		$this->auth_alias = JApplicationHelper::stringURLSafe($this->auth_alias);
		if (trim(str_replace('-','',$this->auth_alias)) == '') {
			$this->auth_alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}
		
		if (!empty($this->auth_image)) {
			$this->auth_image=ltrim($this->auth_image,"/");
		}
		return true;
	}
	
	public function delete($pk = null)
	{
		$result = parent::delete($pk);
		return $result && $this->tagsHelper->deleteTagData($this, $pk);
	}
	
}