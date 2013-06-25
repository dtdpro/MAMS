<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableFieldGroup extends JTable
{
	function __construct(&$db) 
	{
		parent::__construct('#__mams_article_fieldgroups', 'group_id', $db);
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
}