<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class MAMSModelArtAuths extends JModelList
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
		$query->from('#__mams_artauth as a');
		
		// Join over the fields.
		$query->select('f.field_title');
		$query->join('LEFT', '#__mams_article_fields AS f ON f.field_id = a.aa_field');
		
		// Filter by article.
		$artId = $this->getState('filter.article');
		if (is_numeric($artId)) {
			$query->where('a.aa_art = '.(int) $artId);
		}
		
		//Filter by Field
		$field = $this->getState('filter.field');
		if (is_numeric($field))	$query->where('a.aa_field = '.(int) $field);
		
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}

		// Join over the authors.
		$query->select('CONCAT(auth.auth_fname,IF(auth.auth_mi != "",CONCAT(" ",auth.auth_mi),"")," ",auth.auth_lname,IF(auth.auth_titles != "",CONCAT(", ",auth.auth_titles),"")) as auth_name');
		$query->join('LEFT', '#__mams_authors AS auth ON auth.auth_id = a.aa_auth');
		
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
