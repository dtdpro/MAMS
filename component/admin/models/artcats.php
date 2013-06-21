<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class MAMSModelArtCats extends JModelList
{
	
	public function __construct($config = array())
	{
		
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'ordering', 'a.ordering',
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

		$artId = $app->getUserState('com_mams.drilldowns.filter.article', 0);
		$this->setState('filter.article', $artId);
		
		// Load the parameters.
		$params = JComponentHelper::getParams('com_mams');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.ordering', 'asc');
	}
	
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the table
		$query->from('#__mams_artcat as a');
		
		// Filter by article.
		$artId = $this->getState('filter.article');
		if (is_numeric($artId)) {
			$query->where('a.ac_art = '.(int) $artId);
		}

		// Join over the authors.
		$query->select('cat.cat_title');
		$query->join('LEFT', '#__mams_cats AS cat ON cat.cat_id = a.ac_cat');
		
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}
		
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		
		$query->order($db->escape($orderCol.' '.$orderDirn));
				
		return $query;
	}
	
	public function getArticleTitle() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$artId = $this->getState('filter.article');
		
		if (is_numeric($artId)) {
			$query->select('a.art_title');
			$query->from('#__mams_articles as a');
			$query->where('a.art_id = '.(int) $artId);
			$db->setQuery($query);
			return $db->loadResult();
		} else {
			return "NO ARTICLE";
		}
	}
}
