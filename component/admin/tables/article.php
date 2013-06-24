<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableArticle extends JTable
{
	function __construct(&$db) 
	{
		parent::__construct('#__mams_articles', 'art_id', $db);
	}
	
	public function bind($array, $ignore = '')
	{
		if (isset($array['art_fielddata']) && is_array($array['art_fielddata']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['art_fielddata']);
			$array['art_fielddata'] = (string) $registry;
		}
	
		return parent::bind($array, $ignore);
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->art_id) {
			// Existing item
			$this->art_modified		= $date->toSql();
		} else {
			// New section. A section created on field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->art_added)) {
				$this->art_added = $date->toSql();
				$this->art_modified		= $date->toSql();
			}
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('Article', 'MAMSTable');
		if ($table->load(array('art_alias'=>$this->art_alias)) && ($table->art_id != $this->art_id || $this->art_id==0)) {
			$this->setError(JText::_('COM_MAMS_ERROR_UNIQUE_ALIAS'));
			return false;
		}
		
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
	
	public function check()
	{
		// check for valid name
		if (trim($this->art_title) == '') {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_TITLE'));
			return false;
		}

		if (empty($this->art_alias)) {
			$this->art_alias = $this->art_title;
		}
		$this->art_alias = JApplication::stringURLSafe($this->art_alias);
		if (trim(str_replace('-','',$this->art_alias)) == '') {
			$this->art_alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		if (!empty($this->art_thumb)) {
			$this->art_thumb=ltrim($this->art_thumb,"/");
		}
		return true;
	}	
}