<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableSec extends JTable
{
	protected $tagsHelper = null;
	
	function __construct(&$db) 
	{
		parent::__construct('#__mams_secs', 'sec_id', $db);

		$this->tagsHelper = new JHelperTags();
		$this->tagsHelper->typeAlias = 'com_mams.sec';
	}
	
	/*protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_mams.sec.' . (int) $this->$k;
	}
	
	protected function _getAssetTitle()
	{
		return $this->sec_name;
	}
	
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$assetId = null;
	
		if ($assetId === null)
		{
			// Build the query to get the asset id for the parent category.
			$query = $this->_db->getQuery(true)
			->select($this->_db->quoteName('id'))
			->from($this->_db->quoteName('#__assets'))
			->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote("com_mams"));
	
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
	}*/
	
	public function bind($array, $ignore = '')
	{

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
		if ($this->sec_id) {
			// Existing item
			$this->sec_modified		= $date->toSql();
		} else {
			// New section. A section created on field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->sec_cadded)) {
				$this->sec_added = $date->toSql();
				$this->sec_modified		= $date->toSql();
			}
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('Sec', 'MAMSTable');
		if ($table->load(array('sec_alias'=>$this->sec_alias)) && ($table->sec_id != $this->sec_id || $this->sec_id==0)) {
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
		if (trim($this->sec_name) == '') {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_TITLE'));
			return false;
		}

		// check for existing name
		$query = 'SELECT sec_id FROM #__mams_secs WHERE sec_name = '.$this->_db->Quote($this->sec_name);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->sec_id)) {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_NAME'));
			return false;
		}

		if (empty($this->sec_alias)) {
			$this->sec_alias = $this->sec_name;
		}
		$this->sec_alias = JApplicationHelper::stringURLSafe($this->sec_alias);
		if (trim(str_replace('-','',$this->sec_alias)) == '') {
			$this->sec_alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		return true;
	}
	
	public function delete($pk = null)
	{
		$result = parent::delete($pk);
		return $result && $this->tagsHelper->deleteTagData($this, $pk);
	}

	/**
	 * Method to recursively rebuild the whole nested set tree.
	 *
	 * @param   integer  $parentId  The root of the tree to rebuild.
	 * @param   integer  $leftId    The left id to start with in building the tree.
	 * @param   integer  $level     The level to assign to the current nodes.
	 * @param   string   $path      The path to the current nodes.
	 *
	 * @return  integer  1 + value of root rgt on success, false on failure
	 *
	 * @link    https://docs.joomla.org/JTableNested/rebuild
	 * @since   11.1
	 * @throws  RuntimeException on database error.
	 */
	public function rebuild($parentId = 0, $leftId = 0, $level = 0, $path = '')
	{
		$query = $this->_db->getQuery(true);

		// Build the structure of the recursive query.
		if (!isset($this->_cache['rebuild.sql']))
		{
			$query->clear()
				->select($this->_tbl_key . ', sec_alias')
				->from($this->_tbl)
				->where('parent_id = %d');

			// If the table has an ordering field, use that for ordering.
			if (property_exists($this, 'ordering'))
			{
				$query->order('parent_id, ordering, lft');
			}
			else
			{
				$query->order('parent_id, lft');
			}

			$this->_cache['rebuild.sql'] = (string) $query;
		}

		// Make a shortcut to database object.

		// Assemble the query to find all children of this node.
		$this->_db->setQuery(sprintf($this->_cache['rebuild.sql'], (int) $parentId));

		$children = $this->_db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// Execute this function recursively over all children
		foreach ($children as $node)
		{
			/*
			 * $rightId is the current right value, which is incremented on recursion return.
			 * Increment the level for the children.
			 * Add this item's alias to the path (but avoid a leading /)
			 */
			$rightId = $this->rebuild($node->{$this->_tbl_key}, $rightId, $level + 1, $path . (empty($path) ? '' : '/') . $node->sec_alias);

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false)
			{
				return false;
			}
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$query->clear()
			->update($this->_tbl)
			->set('lft = ' . (int) $leftId)
			->set('rgt = ' . (int) $rightId)
			->set('level = ' . (int) $level)
			->set('path = ' . $this->_db->quote($path))
			->where($this->_tbl_key . ' = ' . (int) $parentId);
		$this->_db->setQuery($query)->execute();

		// Return the right value of this node + 1.
		return $rightId + 1;
	}

	/**
	 * Method to rebuild the node's path field from the alias values of the
	 * nodes from the current node to the root node of the tree.
	 *
	 * @param   integer  $pk  Primary key of the node for which to get the path.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    https://docs.joomla.org/JTableNested/rebuildPath
	 * @since   11.1
	 */
	public function rebuildPath($pk = null)
	{
		$fields = $this->getFields();

		// If there is no alias or path field, just return true.
		if (!array_key_exists('sec_alias', $fields) || !array_key_exists('path', $fields))
		{
			return true;
		}

		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// Get the aliases for the path from the node to the root node.
		$query = $this->_db->getQuery(true)
			->select('p.sec_alias')
			->from($this->_tbl . ' AS n, ' . $this->_tbl . ' AS p')
			->where('n.lft BETWEEN p.lft AND p.rgt')
			->where('n.' . $this->_tbl_key . ' = ' . (int) $pk)
			->order('p.lft');
		$this->_db->setQuery($query);

		$segments = $this->_db->loadColumn();

		// Make sure to remove the root path if it exists in the list.
		if ($segments[0] == 'root')
		{
			array_shift($segments);
		}

		// Build the path.
		$path = trim(implode('/', $segments), ' /\\');

		// Update the path field for the node.
		$query->clear()
			->update($this->_tbl)
			->set('path = ' . $this->_db->quote($path))
			->where($this->_tbl_key . ' = ' . (int) $pk);

		$this->_db->setQuery($query)->execute();

		// Update the current record's path to the new one:
		$this->path = $path;

		return true;
	}

	/**
	 * Method to set the location of a node in the tree object.  This method does not
	 * save the new location to the database, but will set it in the object so
	 * that when the node is stored it will be stored in the new location.
	 *
	 * @param   integer  $referenceId  The primary key of the node to reference new location by.
	 * @param   string   $position     Location type string. ['before', 'after', 'first-child', 'last-child']
	 *
	 * @return  void
	 *
	 * @note    Since 12.1 this method returns void and throws an InvalidArgumentException when an invalid position is passed.
	 * @since   11.1
	 * @throws  InvalidArgumentException
	 */
	public function setLocation($referenceId, $position = 'after')
	{
		// Make sure the location is valid.
		if (($position != 'before') && ($position != 'after') && ($position != 'first-child') && ($position != 'last-child'))
		{
			throw new InvalidArgumentException(sprintf('%s::setLocation(%d, *%s*)', get_class($this), $referenceId, $position));
		}
		// Set the location properties.
		$this->_location = $position;
		$this->_location_id = $referenceId;
	}

	/**
	 * Method to update order of table rows
	 *
	 * @param   array  $idArray    id numbers of rows to be reordered.
	 * @param   array  $lft_array  lft values of rows to be reordered.
	 *
	 * @return  integer  1 + value of root rgt on success, false on failure.
	 *
	 * @since   11.1
	 * @throws  Exception on database error.
	 */
	public function saveorder($idArray = null, $lft_array = null)
	{
		try
		{
			$query = $this->_db->getQuery(true);
			// Validate arguments
			if (is_array($idArray) && is_array($lft_array) && count($idArray) == count($lft_array))
			{
				for ($i = 0, $count = count($idArray); $i < $count; $i++)
				{
					// Do an update to change the lft values in the table for each id
					$query->clear()
						->update($this->_tbl)
						->where($this->_tbl_key . ' = ' . (int) $idArray[$i])
						->set('lft = ' . (int) $lft_array[$i]);
					$this->_db->setQuery($query)->execute();
					// @codeCoverageIgnoreStart
					if ($this->_debug)
					{
						$this->_logtable();
					}
					// @codeCoverageIgnoreEnd
				}
				return $this->rebuild();
			}
			else
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			$this->_unlock();
			throw $e;
		}
	}


}