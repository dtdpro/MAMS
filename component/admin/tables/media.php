<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableMedia extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__mams_media', 'med_id', $db);
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->med_id) {
			// Existing item
			$this->med_modified		= $date->toSql();
		} else {
			// New section. A section created on field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->med_added)) {
				$this->med_added = $date->toSql();
				$this->med_modified		= $date->toSql();
			}
		}
		
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
	
	public function check()
	{
		// check for valid name
		if (trim($this->med_inttitle) == '' || trim($this->med_exttitle) == '') {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_TITLE'));
			return false;
		}
	
		if (!empty($this->med_file)) {
			$this->med_file=ltrim($this->med_file,"/");
		}
		if (!empty($this->med_still)) {
			$this->med_still=ltrim($this->med_still,"/");
		}
		return true;
	}
	
}