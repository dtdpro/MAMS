<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class MAMSModelTags extends JModelList
{
	
	public function __construct($config = array())
	{
		
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'tag_added', 'c.tag_added',
				'tag_modified', 'c.tag_modified',
				'tag_title', 'c.tag_title',
				'tag_id','c.tag_id',
                'tag_items','c.tag_items',
				'access','c.access',
				'published','c.published',
				'lft', 'c.lft',
				'rgt', 'c.rgt',
				'level', 'c.level',
				'path', 'c.path',
			);
		}
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
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
		parent::populateState('c.tag_title', 'asc');
	}
	
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('c.*');

		// From the hello table
		$query->from('#__mams_tags as c');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = c.access');
        $query->select('(SELECT COUNT(*) FROM #__mams_arttag AS at WHERE at.at_tag = c.tag_id GROUP BY c.tag_id) as tag_items');
		
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
				$query->where('c.tag_id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(c.tag_title LIKE '.$search.' OR c.tag_alias LIKE '.$search.')');
			}
		}

		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'c.tag_title');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));
		if ($listOrdering == 'c.access')
		{
			$query->order('c.access ' . $listDirn . ', c.tag_title ' . $listDirn);
		}
		else
		{
			$query->order($db->escape($listOrdering) . ' ' . $listDirn);
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
			$this->cache[$store] = $this->_getList($this->_getListQuery(), $this->getStart(), $this->getState('list.limit'));
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		// Get Access levels
		$allist = $this->getAccessLevelList();

		// Create a string of the access levels available
		foreach ($this->cache[$store] as &$i) {
			$alevels = explode(",",$i->tag_feataccess);
			$leveltext = [];
			foreach ($alevels as $a) {
				if (isset($allist[$a])) {
					$leveltext[] = $allist[ $a ];
				}
			}
			$i->feataccess_level = implode(", ",$leveltext);
		}

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
