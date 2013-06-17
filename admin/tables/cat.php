<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableCat extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__mams_cats', 'cat_id', $db);
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->cat_id) {
			// Existing item
			$this->cat_modified		= $date->toSql();
		} else {
			// New section. A section created on field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->cat_added)) {
				$this->cat_added = $date->toSql();
				$this->cat_modified		= $date->toSql();
			}
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('Cat', 'MAMSTable');
		if ($table->load(array('cat_alias'=>$this->cat_alias)) && ($table->cat_id != $this->cat_id || $this->cat_id==0)) {
			$this->setError(JText::_('COM_MAMS_ERROR_UNIQUE_ALIAS'));
			return false;
		}
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
	
	public function check()
	{
		// check for valid name
		if (trim($this->cat_title) == '') {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_TITLE'));
			return false;
		}

		// check for existing name
		$query = 'SELECT cat_id FROM #__mams_cats WHERE cat_title = '.$this->_db->Quote($this->cat_title);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->cat_id)) {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_NAME'));
			return false;
		}

		if (empty($this->cat_alias)) {
			$this->cat_alias = $this->cat_title;
		}
		$this->cat_alias = JApplication::stringURLSafe($this->cat_alias);
		if (trim(str_replace('-','',$this->cat_alias)) == '') {
			$this->cat_alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		return true;
	}
	
}