<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

class MAMSModelArtList extends JModelList
{
	protected $artids = Array();
	protected $secid = Array();
	protected $params = null;
	protected $alvls = Array();
	
	function __construct($config = array()) {
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$this->alvls = $user->getAuthorisedViewLevels();
		$this->alvls = array_merge($this->alvls,$cfg->reggroup);

		parent::__construct($config);
	}
	
	function populateState($ordering = null, $direction = null)
	{
		// Initiliase variables.
		$app = JFactory::getApplication('site');
		
		// Load the parameters. Merge Global and Menu Item params into new object
		$this->params = $app->getParams();
		$this->setState('params', $this->params);
		
		// pagination
		$this->setState('list.start', $app->input->get('limitstart', 0, 'uint'));
		$limit = $this->params->get("items_page",10);
		$this->setState('list.limit', $limit);
	}
	
	function getListQuery()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
        $query1 = $db->getQuery(true);
		$query2 = $db->getQuery(true);
		$cfg = MAMSHelper::getConfig();

        //Set Order
        switch ($this->params->get("orderby","pubdsc")) {
            case "titasc": $order = 'art_title ASC'; break;
            case "titdsc": $order = 'art_title DESC'; break;
            case "pubasc": $order = 'art_publish_up ASC, lft ASC, ordering ASC'; break;
            case "pubdsc": $order = 'art_publish_up DESC, lft ASC, ordering ASC'; break;
	        case "pubasctitasc": $order = 'art_publish_up ASC, art_title ASC'; break;
	        case "pubasctitdsc": $order = 'art_publish_up ASC, art_title DESC';  break;
	        case "pubdsctitasc": $order = 'art_publish_up DESC, art_title ASC';  break;
	        case "pubdsctitdsc": $order = 'art_publish_up DESC, art_title DESC';  break;
            default: $order = 'art_publish_up DESC, lft ASC, ordering ASC'; break;
        }

		// Query Articles
        $query1->select('a.art_id as art_id, a.art_sec as art_sec, a.art_title as art_title, a.art_alias as art_alias, a.art_desc as art_desc, a.art_thumb as art_thumb, a.art_fielddata as art_fielddata, a.art_publish_up as art_publish_up, a.params, a.ordering as ordering, "article" as content_type, sec.sec_id, sec.sec_name, sec.sec_alias, sec.lft as lft ');
		$query1->from('#__mams_articles AS a');
		$query1->join('RIGHT','#__mams_secs AS sec ON sec.sec_id = a.art_sec');
		if (count($this->artids)) $query1->where('a.art_id IN ('.implode(",",$this->artids).')');
		if (count($this->secid)) $query1->where('a.art_sec IN ('.implode(",",$this->secid).')');
		$query1->where('a.state >= 1');
		$query1->where('a.access IN ('.implode(",",$this->alvls).')');
		if ($this->params->get('restrict_feat',0)) $query1->where('a.feataccess IN ('.implode(",",$this->alvls).')');
		if (!in_array($cfg->ovgroup,$this->alvls)) { $query1->where('a.art_publish_up <= NOW()'); $query1->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }

		// Query Section Children if looking at secs
        if (count($this->secid)) {
			$query2->select('s.sec_id as art_id, s.sec_id as art_sec, s.sec_name as art_title, s.sec_alias as art_alias, s.sec_desc as art_desc, s.sec_thumb as art_thumb, "" as art_fielddata, date(s.sec_added) as art_publish_up, "" as params, s.lft as ordering, "section" as content_type, sec.sec_id, sec.sec_name, sec.sec_alias, sec.lft as lft');
			$query2->from('#__mams_secs AS s');
			$query2->join('RIGHT','#__mams_secs AS sec ON sec.sec_id = s.parent_id');
			$query2->where('s.parent_id IN ('.implode(",",$this->secid).')');
			$query2->where('s.published >= 1');
			$query2->where('s.access IN ('.implode(",",$this->alvls).')');

			$query->select('l.*')->from('('.$query1->union($query2).') as l')->order($order);

		} else {
            $query=$query1;
            $query->order($order);
        }
		
		return $query;
	}
	
	function getArticles($artids=array(),$secid=array(),$paginate = true) {
		$this->artids=$artids;
		$this->secid=$secid;
		return $this->getItems($paginate);
	}
	
	function getItems($paginate = true) {
		
		$db = JFactory::getDBO();
		$app = JFactory::getApplication('site');
		
		$query = $this->getListQuery();
		$limitstart = $this->getState('list.start');
		$limit = $this->getState('list.limit');
		
		if ($paginate) $db->setQuery($query, $limitstart, $limit);
		else $db->setQuery($query);
		$items = $db->loadObjectList();
				
		//Get Authors, Cats, Fields
		foreach ($items as &$i) {
			if ($i->content_type == 'article') {
				// Get Authors
				$i->auts = $this->getFieldAuthors($i->art_id, "5");

				// Get Categories
				$qc = $db->getQuery(true);
				$qc->select('c.cat_id,c.cat_title,c.cat_alias');
				$qc->from('#__mams_artcat as ac');
				$qc->join('RIGHT', '#__mams_cats AS c ON ac.ac_cat = c.cat_id');
				$qc->where('ac.published >= 1');
				$qc->where('c.published >= 1');
				$qc->where('c.access IN (' . implode(",", $this->alvls) . ')');
				$qc->where('ac.ac_art = ' . $i->art_id);
				$qc->order('ac.ordering ASC');
				$db->setQuery($qc);
				$i->cats = $db->loadObjectList();

				// Get Tags
				$qc = $db->getQuery(true);
				$qc->select('t.tag_id,t.tag_title,t.tag_alias,t.tag_icon');
				$qc->from('#__mams_arttag as at');
				$qc->join('RIGHT', '#__mams_tags AS t ON at.at_tag = t.tag_id');
				$qc->where('at.published >= 1');
				$qc->where('t.published >= 1');
				$qc->where('t.access IN (' . implode(",", $this->alvls) . ')');
				$qc->where('at.at_art = ' . $i->art_id);
				$qc->order('at.ordering ASC');
				$db->setQuery($qc);
				$i->tags = $db->loadObjectList();

				// Get extra fields
				if ($i->art_fielddata) {
					$field_registry = new JRegistry;
					$field_registry->loadString($i->art_fielddata);
					$i->art_fielddata = $field_registry->toObject();
				}

				$i->fields = $this->getArticleListFields($i->art_id);
			}
		}
		
		return $items;
	}
	
	protected function getArticleListFields($artid) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('*,f.params as field_params,g.params as group_params');
		$query->from("#__mams_article_fields as f");
		$query->select('g.group_title');
		$query->join('LEFT', '#__mams_article_fieldgroups AS g ON g.group_id = f.field_group');
		$query->where('f.published >= 1');
		$query->where('f.access IN ('.implode(",",$this->alvls).')');
		$query->where('g.published >= 1');
		$query->where('g.access IN ('.implode(",",$this->alvls).')');
		$query->where('f.field_show_list = 1');
		$query->where('f.field_id >= 100');
		$query->order('f.ordering ASC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		foreach ($items as &$i) {
			switch ($i->field_type) {
				case "auths": $i->data = $this->getFieldAuthors($artid,$i->field_id); break;
				case "dloads": $i->data = $this->getFieldDownloads($artid,$i->field_id); break;
				case "links": $i->data = $this->getFieldLinks($artid,$i->field_id); break;
			}
			
			$registryf = new JRegistry;
			$registryf->loadString($i->field_params);
			$i->field_params = $registryf->toObject();
			
			$registryg = new JRegistry;
			$registryg->loadString($i->group_params);
			$i->group_params = $registryg->toObject();
		}
			
		return $items;
	}
	
	protected function getFieldAuthors($artid, $fid) {
		$db = JFactory::getDBO();
		$qa=$db->getQuery(true);
		$qa->select('a.auth_id,a.auth_fname,a.auth_mi,a.auth_lname,a.auth_titles,a.auth_alias,a.auth_sec');
		$qa->from('#__mams_artauth as aa');
		$qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
		$qa->where('aa.published >= 1');
		$qa->where('a.published >= 1');
		$qa->where('a.access IN ('.implode(",",$this->alvls).')');
		$qa->where('aa.aa_art = '.$artid);
		$qa->where('aa.aa_field = '.$fid);
		$qa->order('aa.ordering ASC');
		$db->setQuery($qa);
		return $db->loadObjectList();
	}
	
	protected function getFieldDownloads($artid, $fid) {		
		$db = JFactory::getDBO();
		$qa=$db->getQuery(true);
		$qa->select('d.*');
		$qa->from('#__mams_artdl as ad');
		$qa->join('RIGHT','#__mams_dloads AS d ON ad.ad_dload = d.dl_id');
		$qa->where('ad.published >= 1');
		$qa->where('d.published >= 1');
		$qa->where('d.access IN ('.implode(",",$this->alvls).')');
		$qa->where('ad.ad_art = '.$artid);
		$qa->where('ad.ad_field = '.$fid);
		$qa->order('ad.ordering ASC');
		$db->setQuery($qa);
		return $db->loadObjectList();
	}
	
	protected function getFieldLinks($artid, $fid) {
		$db = JFactory::getDBO();
		$qa=$db->getQuery(true);
		$qa->select('l.*');
		$qa->from('#__mams_artlinks as al');
		$qa->join('RIGHT','#__mams_links AS l ON al.al_link = l.link_id');
		$qa->where('al.published >= 1');
		$qa->where('l.published >= 1');
		$qa->where('l.access IN ('.implode(",",$this->alvls).')');
		$qa->where('al.al_art = '.$artid);
		$qa->where('al.al_field = '.$fid);
		$qa->order('al.ordering ASC');
		$db->setQuery($qa);
		return $db->loadObjectList();
	}
	
	public function getPagination() {
			
		// Create the pagination object.
		jimport('joomla.html.pagination');
		$limit = (int) $this->getState('list.limit');
		$page = new JPagination($this->getTotal(), (int) $this->getState('list.start'), $limit);
		
		
		
		return $page;
	}
	
	public function getTotal() {
				
		// Load the total.
		$query = $this->getListQuery();
		$total = (int) $this->_getListCount($query);
		
		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
		$this->setError($this->_db->getErrorMsg());
		return false;
		}
		
		
		return $total;
	}

	function getSecArts($sec) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('a.art_id');
		$query->from('#__mams_articles AS a');
		$query->where('a.art_sec IN ('.implode(",",$sec).')');
		$query->where('a.state >= 1');
		$query->where('a.access IN ('.implode(",",$this->alvls).')');
		$db->setQuery($query); 
		$items = $db->loadColumn();
		return $items;
	}

	function getCatArts($cat) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		$query->select('ac.ac_art');
		$query->from('#__mams_artcat AS ac');
		$query->where('ac.ac_cat IN ( '.implode(",",$cat).')');
		$query->where('ac.published >= 1');
		$db->setQuery($query);
		$items = $db->loadColumn();
		return $items;
	}
	
	function getTagArts($tag) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('at.at_art');
		$query->from('#__mams_arttag AS at');
		$query->where('at.at_tag IN ( '.implode(",",$tag).')');
		$query->where('at.published >= 1');
		$db->setQuery($query);
		$items = $db->loadColumn();
		return $items;
	}
	
	function getSecCats($sec) {
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$artids = $this->getSecArts($sec);
		
		$query->select('ac.ac_cat');
		$query->from('#__mams_artcat AS ac');
		$query->where('ac.ac_art IN ( '.implode(",",$artids).')');
		$query->where('ac.published >= 1');
		$db->setQuery($query);
		$catids = $db->loadColumn();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from("#__mams_cats");
		$query->where('cat_id IN ('.implode(",",$catids).')');
		$query->where('access IN ('.implode(",",$this->alvls).')');
        switch ($this->params->get("cat_orderby","titasc")) {
            case "titasc": $query->order('cat_title ASC'); break;
            case "titdsc": $query->order('cat_title DESC'); break;
            case "orderasc": $query->order('ordering ASC'); break;
            case "orderdsc": $query->order('ordering ASC'); break;
            default: $query->order('cat_title ASC'); break;
        }
		$db->setQuery($query);
		$items=$db->loadObjectList();

		return $items;
	}
	
	function getAuthArts($aut) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('aa.aa_art');
		$query->from('#__mams_artauth AS aa');
		$query->where('aa.aa_auth IN ( '.implode(",",$aut).')');
		$query->where('aa.aa_field = 5');
		$query->where('aa.published >= 1');
		$db->setQuery($query);
		$items = $db->loadColumn();
		return $items;
	}

	function getArticlesAuthoredAuthors($artid) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__mams_artauth AS aa');
		$query->join('RIGHT','#__mams_authors AS a ON a.auth_id = aa.aa_auth');
		$query->where('aa.aa_art = '.$db->escape((int)$artid));
		$query->where('aa.aa_field = 5');
		$query->where('aa.published >= 1');
		$query->order('aa.ordering ASC');
		$db->setQuery($query);
		$authors = $db->loadObjectList();

		return $authors;
	}

	function getArticlesAuthored($authors, $artid) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('aa.aa_art');
		$query->from('#__mams_artauth AS aa');
		$query->where('aa.aa_auth IN ( '.implode(",",$authors).')');
		$query->where('aa.aa_field = 5');
		$query->where('aa.published >= 1');
		$query->where('aa.aa_art != '.$db->escape((int)$artid));
		$db->setQuery($query);
		$items = $db->loadColumn();

		return $items;
	}
	
	function getSecInfo($sec) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('s.*');
		$query->from('#__mams_secs AS s');
		$query->where('s.sec_id IN ('.implode(",",$sec).')');
		$query->where('s.published >= 1');
		$query->where('s.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$db->setQuery($query);
		$info = $db->loadObjectList();
		return $info;
	}
	
	function getCatInfo($cat) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('c.*');
		$query->from('#__mams_cats AS c');
		$query->where('c.cat_id IN ('.implode(",",$cat).')');
		$query->where('c.published >= 1');
		$query->where('c.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$db->setQuery($query);
		$info = $db->loadObjectList();
		return $info;
	}

	function getTagInfo($tag) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		$query->select('t.*');
		$query->from('#__mams_tags AS t');
		$query->where('t.tag_id IN ('.implode(",",$tag).')');
		$query->where('t.published >= 1');
		$query->where('t.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$db->setQuery($query);
		$info = $db->loadObjectList();
		return $info;
	}
	
	function getAutInfo($aut) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('a.*');
		$query->from('#__mams_authors AS a');
		$query->where('a.auth_id IN ( '.implode(",",$aut).')');
		$query->where('a.published >= 1');
		$query->where('a.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$db->setQuery($query);
		$info = $db->loadObjectList();
		return $info;
	}
	
	function getCats($artcount = false, $parent=0) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$query->select('c.*');
		$query->from('#__mams_cats AS c');
		if (is_array($parent)) {
			$query->where( 'c.parent_id IN (' . implode(",",$parent).')' );
		} else {
			$query->where( 'c.parent_id = ' . (int) $parent );
		}
		$query->where('c.published >= 1');
		$query->where('c.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		switch ($this->params->get("ordercatlistby","titasc")) {
			case "titasc": $query->order('cat_title ASC'); break;
			case "titdsc": $query->order('cat_title DESC'); break;
            case "orderasc": $query->order('ordering ASC'); break;
            case "orderdsc": $query->order('ordering ASC'); break;
			default: $query->order('cat_title ASC'); break;
		}
		if ($this->params->get('only_featcat',0)) {
			$query->where('c.cat_featured = 1');
		}
		$db->setQuery($query);
		$items = $db->loadObjectList();

		// Remove categories not in Access Level List when enabled
		if ($this->params->get('only_featcat',0) || $this->params->get('restrict_featcat',0)) {
			foreach ($items as $k => $i) {
				$alintersect = array_intersect(explode(",",$i->cat_feataccess),$user->getAuthorisedViewLevels());
				if (!count($alintersect)) {
					unset($items[$k]);
				}
			}
		}
		
		if ($artcount) {
			foreach ($items as &$i) {
				$query = $db->getQuery(true);
				$query->select('ac.ac_art');
				$query->from('#__mams_artcat AS ac');
				$query->where('ac.ac_cat = '.$i->cat_id);
				$query->where('ac.published >= 1');
				$db->setQuery($query);
				$arts = $db->loadColumn();
				$i->numarts = count($arts);
			}
		}
		
		return $items;
	}


	function getSecs($artcount = false) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$query->select('c.*');
		$query->from('#__mams_secs AS c');
		$query->where('c.sec_type="article"');
		$query->where('c.published >= 1');
		$query->where('c.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		switch ($this->params->get("orderlistby","titasc")) {
			case "titasc": $query->order('sec_name ASC'); break;
			case "titdsc": $query->order('sec_name DESC'); break;
			default: $query->order('sec_name ASC'); break;
		}
		$db->setQuery($query);
		$items = $db->loadObjectList();
	
		if ($artcount) {
			foreach ($items as &$i) {
				$query = $db->getQuery(true);
				$query->select('art_id');
				$query->from('#__mams_articles');
				$query->where('art_sec = '.$i->sec_id);
				$query->where('state >= 1');
				$query->where('access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
				$db->setQuery($query);
				$arts = $db->loadColumn();
				$i->numarts = count($arts);
			}
		}
	
		return $items;
	}

	function getSecChildren($sec) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$query->select('c.*');
		$query->from('#__mams_secs AS c');
		$query->where('c.sec_type="article"');
		$query->where('parent_id IN ('.implode(',',$sec).')');
		$query->where('c.published >= 1');
		$query->where('c.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$query->order('c.lft ASC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		return $items;
	}

}