<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableField extends JTable
{
	function __construct(&$db) 
	{
		parent::__construct('#__mams_article_fields', 'field_id', $db);
	}
	
	public function store($updateNulls = false)
	{
		// Verify that the alias is unique
		$table = JTable::getInstance('Field', 'MAMSTable');
		if ($table->load(array('field_name'=>$this->field_name)) && ($table->field_id != $this->field_id || $this->field_id==0)) {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_NAME'));
			return false;
		}
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
}