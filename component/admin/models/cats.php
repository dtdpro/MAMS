<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class MAMSModelCats extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'cat_added', 'c.cat_added',
				'cat_modified', 'c.cat_modified',
				'cat_title', 'c.cat_title',
				'cat_id','c.cat_id',
				'access','c.access','state',
				'published','c.published',
				'lft', 'c.lft',
			);
		}
		parent::__construct($config);
	}
	
	protected function populateState($ordering = 'c.lft', $direction = 'asc')
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		$published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $published);

		$accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		// Load the parameters.
		$params = JComponentHelper::getParams('com_mams');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('c.*');

		// From the hello table
		$query->from('#__mams_cats as c');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = c.access');
        $query->select('(SELECT COUNT(*) FROM #__mams_artcat AS ac WHERE ac.ac_cat = c.cat_id GROUP BY c.cat_id) as cat_items');
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('c.access = '.(int) $access);
		}
		
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('c.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(c.published IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('c.cat_id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(c.cat_title LIKE '.$search.' OR c.cat_alias LIKE '.$search.')');
			}
		}

		// Add the list ordering clause
		$orderCol  = $this->state->get('list.ordering', 'c.lft');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		if ($orderCol == 'c.access')
		{
			$query->order('c.access ' . $orderDirn . ', c.lft ' . $orderDirn);
		}
		else
		{
			$query->order($db->escape($orderCol) . ' ' . $orderDirn);
		}
				
		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();
		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}
		try
		{
			// Load the list items and add the items to the internal cache.
			$items = $this->_getList($this->_getListQuery(), $this->getStart(), $this->getState('list.limit'));
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		// Get Access levels
		$allist = $this->getAccessLevelList();

		// Create a string of the access levels available
		foreach ($items as &$i) {
			$alevels = explode(",",$i->cat_feataccess);
			$leveltext = array();
			foreach ( $alevels as $a ) {
				if (isset($allist[ $a ])) {
					$leveltext[] = $allist[ $a ];
				}
			}
			$i->feataccess_level = implode(", ",$leveltext);
		}

		$this->cache[$store] = $items;
		return $this->cache[$store];
	}

	public function getAccessLevelList()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__viewlevels');
		$db->setQuery($query);
		$alevels = $db->loadObjectList();

		$allist = array();
		foreach ($alevels as $a) {
			$allist[$a->id] = $a->title;
		}
		return $allist;
	}
}
