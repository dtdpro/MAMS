<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableLink extends JTable
{
	function __construct(&$db) 
	{
		parent::__construct('#__mams_links', 'link_id', $db);
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->link_id) {
			// Existing item
			$this->link_modified		= $date->toSql();
		} else {
			// New section. A section created on field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->link_added)) {
				$this->link_added = $date->toSql();
				$this->link_modified		= $date->toSql();
			}
		}
		
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
	
}