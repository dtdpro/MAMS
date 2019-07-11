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

        $feataccessId = $this->getUserStateFromRequest($this->context.'.filter.feataccess', 'filter_feataccess', null, 'int');
        $this->setState('filter.feataccess', $feataccessId);

		$secId = $this->getUserStateFromRequest($this->context.'.filter.sec', 'filter_sec', null, 'int');
		$this->setState('filter.sec', $secId);

        $catId = $this->getUserStateFromRequest($this->context.'.filter.cat', 'filter_cat', null, 'int');
        $this->setState('filter.cat', $catId);

        $authId = $this->getUserStateFromRequest($this->context.'.filter.auth', 'filter_auth', null, 'int');
        $this->setState('filter.auth', $authId);
		
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
        $id .= ':' . $this->getState('filter.cat');
	
		return parent::getStoreId($id);
	}

    public function getCats() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__mams_cats');
        $db->setQuery($query);
        $cats = $db->loadObjectList();
        $catsbyid=array();
        foreach ($cats as $c) {
            $catsbyid[$c->cat_id] = $c->cat_title;
        }
        return $catsbyid;
    }

	public function getTags() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__mams_tags');
		$db->setQuery($query);
		$tags = $db->loadObjectList();
		$tagsbyid=array();
		foreach ($tags as $t) {
			$tagsbyid[$t->tag_id] = $t->tag_title;
		}
		return $tagsbyid;
	}

    public function getAuthors() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__mams_authors');
        $db->setQuery($query);
        $auths = $db->loadObjectList();
        $authsbyid=array();
        foreach ($auths as $a) {
            $authsbyid[$a->auth_id] = $a->auth_fname.(($a->auth_mi) ? " ".$a->auth_mi : "")." ".$a->auth_lname.(($a->auth_titles) ? ", ".$a->auth_titles : "");
        }
        return $authsbyid;
    }

    public function getItems()
    {
        // Get a storage key.
        $store = $this->getStoreId();
        // Try to load the data from internal storage.
        if (isset($this->cache[$store]))
        {
            return $this->cache[$store];
        }
        // Load the list items.
        $query = $this->_getListQuery();
        try
        {
            $items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());
            return false;
        }

        foreach ($items as &$item) {

			// Categories
            $query = $this->_db->getQuery(true);
            $query->select('ac_cat');
            $query->from('#__mams_artcat');
            $query->where('ac_art = '.$item->art_id);
            $this->_db->setQuery($query);
            $item->cats = $this->_db->loadColumn();

	        // Tags
	        $query = $this->_db->getQuery(true);
	        $query->select('at_tag');
	        $query->from('#__mams_arttag');
	        $query->where('at_art = '.$item->art_id);
	        $this->_db->setQuery($query);
	        $item->tags = $this->_db->loadColumn();

            // Authors
            $query = $this->_db->getQuery(true);
            $query->select('aa_auth');
            $query->from('#__mams_artauth');
            $query->where('aa_art = '.$item->art_id);
            $this->_db->setQuery($query);
            $item->authors = $this->_db->loadColumn();
        }

        // Add the items to the internal cache.
        $this->cache[$store] = $items;
        return $this->cache[$store];
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
		
		// Join over the users for the user who added.
		$query->select('ua.name AS adder')
		->join('LEFT', '#__users AS ua ON ua.id = a.art_added_by');
		
		// Join over the users for the user who modified.
		$query->select('um.name AS modifier')
		->join('LEFT', '#__users AS um ON um.id = a.art_modified_by');
				
		// Filter by section.
		if ($sec = $this->getState('filter.sec')) {
			$query->where('a.art_sec = '.(int) $sec);
		}

        // Filter by a category
        if ($catId = $this->getState('filter.cat'))
        {
            $query->where($db->quoteName('ac.ac_cat') . ' = ' . (int) $catId)
                ->join(
                    'LEFT', $db->quoteName('#__mams_artcat', 'ac')
                    . ' ON ' . $db->quoteName('ac.ac_art') . ' = ' . $db->quoteName('a.art_id')
                );
        }

		// Filter by a author
		if ($catId = $this->getState('filter.auth'))
		{
			$query->where($db->quoteName('aa.aa_auth') . ' = ' . (int) $catId)
				->join(
					'LEFT', $db->quoteName('#__mams_artauth', 'aa')
					. ' ON ' . $db->quoteName('aa.aa_art') . ' = ' . $db->quoteName('a.art_id')
				);
		}
				
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = '.(int) $access);
		}

        // Filter by featured access level.
        if ($feataccess = $this->getState('filter.feataccess')) {
            $query->where('a.feataccess = '.(int) $feataccess);
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
			$query->order($db->escape('a.art_publish_up '.$orderDirn.', s.lft '.$orderDirn.', a.ordering '.$orderDirn));
		} else if ($orderCol == 'a.art_publish_up') {
			$query->order($db->escape('a.art_publish_up '.$orderDirn.', s.lft ASC, a.ordering ASC'));
		} else{
			$query->order($db->escape($orderCol.' '.$orderDirn));
		}
				
		return $query;
	}
}
