<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class MAMSModelArticles extends JModelList
{
	
	public function __construct($config = array())
	{
		
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'art_id', 'a.art_id',
				'art_added', 'a.art_added',
				'art_modified', 'a.art_modified',
				'art_publish_up', 'a.art_publish_up',
				'art_publish_down', 'a.art_publish_down',
				'art_title', 'a.art_title',
				'art_hits', 'a.art_hits',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'access', 'a.access',
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

		$secId = $this->getUserStateFromRequest($this->context.'.filter.sec', 'filter_sec', null, 'int');
		$this->setState('filter.sec', $secId);
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		// List state information.
		parent::populateState('a.art_publish_up', 'desc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.sec');
	
		return parent::getStoreId($id);
	}
	
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the hello table
		$query->from('#__mams_articles as a');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		$query->select('af.title AS feataccess_level');
		$query->join('LEFT', '#__viewlevels AS af ON af.id = a.feataccess');
		
		// Join over the sections.
		$query->select('s.sec_name,s.sec_alias');
		$query->join('LEFT', '#__mams_secs AS s ON s.sec_id = a.art_sec');
		
		
		// Join over the featured.
		$query->select('f.af_id as featured');
		$query->join('LEFT', '#__mams_artfeat AS f ON f.af_art = a.art_id');
		
		// Filter by section.
		if ($sec = $this->getState('filter.sec')) {
			$query->where('a.art_sec = '.(int) $sec);
		}
				
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = '.(int) $access);
		}
		
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.art_id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.art_title LIKE '.$search.' OR a.art_alias LIKE '.$search.')');
			}
		}
		
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		
		if ($orderCol == 'a.ordering') {
			$query->order($db->escape('a.art_publish_up '.$orderDirn.', s.ordering '.$orderDirn.', a.ordering '.$orderDirn));
		} else if ($orderCol == 'a.art_publish_up') {
			$query->order($db->escape('a.art_publish_up '.$orderDirn.', s.ordering ASC, a.ordering ASC'));
		} else{
			$query->order($db->escape($orderCol.' '.$orderDirn));
		}
				
		return $query;
	}
}
