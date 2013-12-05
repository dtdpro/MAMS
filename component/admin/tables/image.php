<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableImage extends JTable
{
	function __construct(&$db) 
	{
		parent::__construct('#__mams_images', 'img_id', $db);
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->img_id) {
			// Existing item
			$this->img_modified		= $date->toSql();
		} else {
			// New section. A section created on field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->img_added)) {
				$this->img_added = $date->toSql();
				$this->img_modified		= $date->toSql();
			}
		}
		
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
	
	public function check()
	{
		// check for valid name
		if (trim($this->img_inttitle) == '' || trim($this->img_exttitle) == '') {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_TITLE'));
			return false;
		}
	
		if (!empty($this->img_full)) {
			$this->img_full=ltrim($this->img_full,"/");
		}
		if (!empty($this->img_thumb)) {
			$this->img_thumb=ltrim($this->img_thumb,"/");
		}
		return true;
	}
	
}