<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class MAMSModelDloads extends JModelList
{
	
	public function __construct($config = array())
	{
		
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'dl_added', 'd.dl_added',
				'dl_modified', 'd.dl_modified',
				'dl_fname', 'd.dl_fname',
				'dl_id', 'd.dl_id',
				'published', 'd.published',
				'access', 'd.access',
			);
		}
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		
		$extension = $app->getUserStateFromRequest('com_mams.dloads.filter.extension', 'extension', 'com_mams', 'cmd');
		$this->setState('filter.extension', $extension);

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
		parent::populateState('d.dl_fname', 'asc');
	}

	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();
		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}
		// Load the list items.
		$query = $this->_getListQuery();
		try
		{
			$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
		/*
		foreach ($items as &$item) {

			// Hits
			$query = $this->_db->getQuery(true);
			$query->select('mt_item');
			$query->from('#__mams_track');
			$query->where('mt_type = "dload"');
			$query->where('mt_item = '.$item->dl_id);
			$this->_db->setQuery($query);
			$item->dl_hits = count($this->_db->loadColumn());
		}
		*/
		// Add the items to the internal cache.
		$this->cache[$store] = $items;
		return $this->cache[$store];
	}

	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('d.*');

		// From the hello table
		$query->from('#__mams_dloads as d');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = d.access');
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('d.access = '.(int) $access);
		}
		
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('d.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(d.published IN (0, 1))');
		}
		
		// Filter by extension
		if ($extension = $this->getState('filter.extension')) {
			$query->where('d.dl_extension = '.$db->quote($extension));
		}
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('d.dl_id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(d.dl_fname LIKE '.$search.' OR d.dl_lname LIKE '.$search.')');
			}
		}
		
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		
		$query->order($db->escape($orderCol.' '.$orderDirn));
				
		return $query;
	}
}
