<?php
// no direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/medias.php';

class MAMSModelFeaturedMedia extends MAMSModelMedias
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'med_id', 'm.med_id',
				'med_inttitle', 'm.med_inttitle',
				'ordering', 'f.ordering',
			);
		}

		parent::__construct($config);
	}

	function getListQuery($resolveFKs = true)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('m.med_id,m.med_inttitle');
		$query->from('#__mams_media AS m');

		// Join over the content table.
		$query->select('f.ordering,f.mf_id');
		$query->join('INNER', '#__mams_mediafeat AS f ON f.mf_media = m.med_id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'm.med_inttitle')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		//echo nl2br(str_replace('#__','jos_',(string)$query));
		return $query;
	}
}