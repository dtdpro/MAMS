<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class MAMSModelMedias extends JModelList
{
	
	public function __construct($config = array())
	{
		
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'med_added', 'm.med_added',
				'med_modified', 'm.med_modified',
				'med_inttitle', 'm.med_inttitle',
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
		parent::populateState('m.med_inttitle', 'asc');
	}
	
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('m.*');

		// From the hello table
		$query->from('#__mams_media as m');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = m.access');
		$query->select('mf.title AS feataccess_level');
		$query->join('LEFT', '#__viewlevels AS mf ON mf.id = m.feataccess');

		// Join over the featured.
		$query->select('f.mf_id as featured');
		$query->join('LEFT', '#__mams_mediafeat AS f ON f.mf_media = m.med_id');
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('m.access = '.(int) $access);
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('m.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(m.published IN (0, 1))');
		}
		
		// Filter by extension
		if ($extension = $this->getState('filter.extension')) {
			$query->where('m.med_extension = '.$db->quote($extension));
		}
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('m.med_id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(m.med_inttitle LIKE '.$search.' OR m.med_file LIKE '.$search.')');
			}
		}
		
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));
				
		return $query;
	}
}
