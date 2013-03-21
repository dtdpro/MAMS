<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableDload extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__mams_dloads', 'dl_id', $db);
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->dl_id) {
			// Existing item
			$this->dl_modified		= $date->toMySQL();
		} else {
			// New section. A section created on field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->dl_added)) {
				$this->dl_added = $date->toMySQL();
				$this->dl_modified		= $date->toMySQL();
			}
		}
		
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
	
	public function check()
	{
		// check for valid name
		if (trim($this->dl_fname) == '' || trim($this->dl_lname) == '') {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_TITLE'));
			return false;
		}

		// check for existing name
		$query = 'SELECT dl_id FROM #__mams_dloads WHERE dl_fname = '.$this->_db->Quote($this->dl_fname);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->dl_id)) {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_DLNAME'));
			return false;
		}
		if (!empty($this->dl_loc)) {
			$this->dl_loc=ltrim($this->dl_loc,"/");
			$this->dl_loc=rtrim($this->dl_loc,"/").'/';
		}

		return true;
	}
	
}