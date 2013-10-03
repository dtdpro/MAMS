<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableArticle extends JTable
{	
	protected $tagsHelper = null;

	function __construct(&$db) 
	{
		parent::__construct('#__mams_articles', 'art_id', $db);

		$this->tagsHelper = new JHelperTags();
		$this->tagsHelper->typeAlias = 'com_mams.article';
	}
	
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_mams.article.' . (int) $this->$k;
	}
	
	protected function _getAssetTitle()
	{
		return $this->sec_name;
	}
	
	protected function _getAssetParentId($table = null, $id = null)
	{
		$assetId = null;
	
		// This is a article under a category.
		if ($this->art_sec)
		{
			// Build the query to get the asset id for the parent category.
			$query = $this->_db->getQuery(true)
			->select($this->_db->quoteName('asset_id'))
			->from($this->_db->quoteName('#__mams_secs'))
			->where($this->_db->quoteName('sec_id') . ' = ' . (int) $this->art_sec);
	
			// Get the asset id from the database.
			$this->_db->setQuery($query);
			if ($result = $this->_db->loadResult())
			{
				$assetId = (int) $result;
			}
		}
	
		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}
	
	public function bind($array, $ignore = '')
	{
		if (isset($array['art_fielddata']) && is_array($array['art_fielddata']))
		{
			$registryfd = new JRegistry;
			$registryfd->loadArray($array['art_fielddata']);
			$array['art_fielddata'] = $registryfd->toString();
		}
		
		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}
		
		
		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}
		
		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}
	
		return parent::bind($array, $ignore);
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if (!$this->art_id) {
			// New article. A article created on field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->art_added)) {
				$this->art_added = $date->toSql();
			}
		}
		
		// Set modification info, new is modified as well
		$this->art_modified		= $date->toSql();
		$this->art_modified_by	= $user->get('id');
		if (empty($this->art_added_by)) {
			$this->art_added_by	= $user->get('id');
		}
		
		// Set publish_up to now if not set
		if (!$this->art_publish_up || $this->art_publish_up == "0000-00-00")
		{
			$this->art_publish_up = $date->toSql();
		}
		
		// Set publish_down to null date if not set
		if (!$this->art_publish_down)
		{
			$this->art_publish_down = $this->_db->getNullDate();
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('Article', 'MAMSTable');
		if ($table->load(array('art_alias'=>$this->art_alias)) && ($table->art_id != $this->art_id || $this->art_id==0)) {
			$this->setError(JText::_('COM_MAMS_ERROR_UNIQUE_ALIAS'));
			return false;
		}
		
		// Attempt to store the user data.	
		$this->tagsHelper->preStoreProcess($this);
		$result = parent::store($updateNulls);
		return $result && $this->tagsHelper->postStoreProcess($this);
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
		

		// Check the publish down date is not earlier than publish up.
		if ($this->art_publish_down != "0000-00-00" && $this->art_publish_down < $this->art_publish_up)
		{
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
			return false;
		}
		
		// clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey))
		{
			// only process if not empty
			$bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
			$after_clean = JString::str_ireplace($bad_characters, "", $this->metakey); // remove bad characters
			$keys = explode(',', $after_clean); // create array using commas as delimiter
			$clean_keys = array();
		
			foreach ($keys as $key)
			{
				if (trim($key)) { // ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}
			$this->metakey = implode(", ", $clean_keys); // put array back together delimited by ", "
		}

		if (!empty($this->art_thumb)) {
			$this->art_thumb=ltrim($this->art_thumb,"/");
		}
		return true;
	}	
	
	public function delete($pk = null)
	{
		$result = parent::delete($pk);
		return $result && $this->tagsHelper->deleteTagData($this, $pk);
	}
	
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$k = $this->_tbl_key;
	
		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state = (int) $state;
	
		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}
	
		// Build the WHERE clause for the primary keys.
		$where = $k.'='.implode(' OR '.$k.'=', $pks);
	
		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = '';
		}
		else
		{
			$checkin = ' AND (checked_out = 0 OR checked_out = '.(int) $userId.')';
		}
	
		// Update the publishing state for rows with the given primary keys.
		$this->_db->setQuery(
				'UPDATE '.$this->_db->quoteName($this->_tbl) .
				' SET '.$this->_db->quoteName('state').' = '.(int) $state .
				' WHERE ('.$where.')' .
				$checkin
		);
	
		try
		{
			$this->_db->execute();
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
	
		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin the rows.
			foreach ($pks as $pk)
			{
				$this->checkin($pk);
			}
		}
	
		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->state = $state;
		}
	
		$this->setError('');
		return true;
	}
}