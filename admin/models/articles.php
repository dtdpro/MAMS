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
				'art_published', 'a.art_published',
				'art_title', 'a.art_title',
				'ordering', 'a.ordering',
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

		$secId = $this->getUserStateFromRequest($this->context.'.filter.sec', 'filter_sec', null, 'int');
		$this->setState('filter.sec', $secId);
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		// List state information.
		parent::populateState('a.art_published', 'desc');
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
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.published IN (0, 1))');
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
		
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));
				
		return $query;
	}
}
