<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class MAMSModelSecs extends JModelList
{
	
	public function __construct($config = array())
	{
		
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'sec_id', 's.sec_id',
				'sec_added', 's.sec_added',
				'sec_modified', 's.sec_modified',
				'sec_name', 's.sec_name',
				'sec_type', 's.sec_type',
				'ordering', 's.ordering',
				'published', 's.published',
				'access', 's.access',
				'lft', 's.lft',
				'rgt', 's.rgt',
				'level', 's.level',
				'path', 's.path',
				'type'
			);
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);

		$accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type');
		$this->setState('filter.type', $type);
		
		// Load the parameters.
		$params = JComponentHelper::getParams('com_mams');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('s.lft', 'asc');
	}
	
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('s.*');

		// From the hello table
		$query->from('#__mams_secs as s');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = s.access');

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('s.access = '.(int) $access);
		}
		
		// Filter by type.
		if ($type = $this->getState('filter.type')) {
			$query->where('s.sec_type = "'. $type.'"');
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('s.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(s.published IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('s.sec_id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(s.sec_name LIKE '.$search.' OR s.sec_alias LIKE '.$search.')');
			}
		}

		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 's.lft');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));
		if ($listOrdering == 's.access')
		{
			$query->order('s.access ' . $listDirn . ', s.lft ' . $listDirn);
		}
		else
		{
			$query->order($db->escape($listOrdering) . ' ' . $listDirn);
		}
				
		return $query;
	}
}
