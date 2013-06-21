<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class MAMSModelLinks extends JModelList
{
	
	public function __construct($config = array())
	{
		
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'link_added', 'l.link_added',
				'link_modified', 'l.link_modified',
				'link_title', 'l.link_title',
				'link_id', 'l.link_id',
				'link_url', 'l.link_url',
				'published', 'l.published',
				'access', 'l.access',
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
		parent::populateState('l.link_title', 'asc');
	}
	
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('l.*');

		// From the hello table
		$query->from('#__mams_links as l');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = l.access');
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('l.access = '.(int) $access);
		}
		
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('l.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(l.published IN (0, 1))');
		}
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('l.link_id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(l.link_title LIKE '.$search.' OR l.link_url LIKE '.$search.')');
			}
		}
		
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		
		$query->order($db->escape($orderCol.' '.$orderDirn));
				
		return $query;
	}
}
