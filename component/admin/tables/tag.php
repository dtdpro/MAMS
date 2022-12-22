<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableTag extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__mams_tags', 'tag_id', $db);
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->tag_id) {
			// Existing item
			$this->tag_modified		= $date->toSql();
		} else {
			// New section. A section created on field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->tag_added)) {
				$this->tag_added = $date->toSql();
				$this->tag_modified		= $date->toSql();
			}
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('Tag', 'MAMSTable');
		if ($table->load(array('tag_alias'=>$this->tag_alias)) && ($table->tag_id != $this->tag_id || $this->tag_id==0)) {
			$this->setError(JText::_('COM_MAMS_ERROR_UNIQUE_ALIAS'));
			return false;
		}
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
	
	public function check()
	{
		// check for valid name
		if (trim($this->tag_title) == '') {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_TITLE'));
			return false;
		}

		// check for existing name
		$query = 'SELECT tag_id FROM #__mams_tags WHERE tag_title = '.$this->_db->Quote($this->tag_title);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->tag_id)) {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_NAME'));
			return false;
		}

		if (empty($this->tag_alias)) {
			$this->tag_alias = $this->tag_title;
		}
		$this->tag_alias = JApplicationHelper::stringURLSafe($this->tag_alias);
		if (trim(str_replace('-','',$this->tag_alias)) == '') {
			$this->tag_alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		return true;
	}
}