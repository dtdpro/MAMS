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

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);

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
		$published = $this->getState('filter.published');
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
