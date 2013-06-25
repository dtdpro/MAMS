<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class MAMSModelArtMeds extends JModelList
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
		
		$field = $this->getUserStateFromRequest($this->context.'.filter.field', 'filter_field', '', 'string');
		$this->setState('filter.state', $field);

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
		$query->from('#__mams_artmed as a');
		
		// Filter by article.
		$artId = $this->getState('filter.article');
		if (is_numeric($artId)) {
			$query->where('a.am_art = '.(int) $artId);
		}
		
		// Join over the fields.
		$query->select('f.field_title');
		$query->join('LEFT', '#__mams_article_fields AS f ON f.field_id = a.am_field');
		
		//Filter by Field
		$field = $this->getState('filter.field');
		if (is_numeric($field))	$query->where('a.am_field = '.(int) $field);

		// Join over the authors.
		$query->select('med.med_inttitle');
		$query->join('LEFT', '#__mams_media AS med ON med.med_id = a.am_media');
		
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}
		
		$query->order($db->escape('f.ordering asc, a.ordering asc'));
				
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
