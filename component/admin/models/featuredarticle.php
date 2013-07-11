<?php
// no direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/articles.php';

class MAMSModelFeaturedArticle extends MAMSModelArticles
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'art_id', 'a.art_id',
				'art_title', 'a.art_title',
				'ordering', 'f.ordering',
			);
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState('f.ordering', 'desc');
	}

	function getListQuery($resolveFKs = true)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.art_id,a.art_title');
		$query->from('#__mams_articles AS a');

		// Join over the content table.
		$query->select('f.ordering,f.af_id');
		$query->join('INNER', '#__mams_artfeat AS f ON f.af_art = a.art_id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'f.ordering')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		//echo nl2br(str_replace('#__','jos_',(string)$query));
		return $query;
	}
}