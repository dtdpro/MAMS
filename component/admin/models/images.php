<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class MAMSModelImages extends JModelList
{
	
	public function __construct($config = array())
	{
		
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'img_added', 'i.img_added',
				'img_modified', 'i.img_modified',
				'img_inttitle', 'i.img_inttitle',
				'img_id', 'i.img_id',
				'published', 'i.published','state',
				'access', 'i.access',
				'ordering', 'i.ordering',
			);
		}
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		
		$extension = $app->getUserStateFromRequest($this->context.'.filter.extension', 'extension', 'com_mams', 'cmd');
		$this->setState('filter.extension', $extension);

		$published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $published);

		$accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);
		
		/*$secId = $this->getUserStateFromRequest($this->context.'.filter.sec', 'filter_sec', null, 'int');
		$this->setState('filter.sec', $secId);*/
		
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		// Load the parameters.
		$params = JComponentHelper::getParams('com_mams');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('i.ordering', 'asc');
	}
	
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('i.*');

		// From the hello table
		$query->from('#__mams_images as i');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = i.access');
		
		// Join over the sections.
		$query->select('s.sec_name');
		$query->join('LEFT', '#__mams_secs AS s ON s.sec_id = i.img_sec');
		
		/*// Filter by section.
		if ($sec = $this->getState('filter.sec')) {
			$query->where('a.auth_sec = '.(int) $sec);
		}*/
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('i.access = '.(int) $access);
		}
		
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('i.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(i.published IN (0, 1))');
		}
		
		// Filter by extension
		if ($extension = $this->getState('filter.extension')) {
			$query->where('i.img_extension = '.$db->quote($extension));
		}
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('i.img_id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(i.img_exttitle LIKE '.$search.' OR i.img_inttitle LIKE '.$search.' OR i.img_full LIKE '.$search.' OR i.img_thumb LIKE '.$search.')');
			}
		}
		
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		
		if ($orderCol == 'i.ordering') {
			$orderCol = 's.sec_name '.$orderDirn.', i.ordering';
		}
		
		$query->order($db->escape($orderCol.' '.$orderDirn));
				
		return $query;
	}
}
