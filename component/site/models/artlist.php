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
		$this->setState('params', $params);
		
		// pagination
		$this->setState('list.start', $app->input->get('limitstart', 0, 'uint'));
		$limit = $this->params->get("items_page",10);
		$this->setState('list.limit', $limit);
	}
	
	function getListQuery()
	{
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$cfg = MAMSHelper::getConfig();
		
		$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
		$query->from('#__mams_articles AS a');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.art_id IN ('.implode(",",$this->artids).')');
		if (count($this->secid)) $query->where('a.art_sec IN ('.implode(",",$this->secid).')');
		$query->where('a.state >= 1');
		$query->where('a.access IN ('.implode(",",$this->alvls).')');
		if (!in_array($cfg->ovgroup,$this->alvls)) { $query->where('a.art_publish_up <= NOW()'); $query->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }
		switch ($this->params->get("orderby","pubdsc")) {
			case "titasc": $query->order('a.art_title ASC'); break;
			case "titdsc": $query->order('a.art_title DESC'); break;
			case "pubasc": $query->order('a.art_publish_up ASC, s.ordering ASC, a.ordering ASC'); break;
			case "pubdsc": $query->order('a.art_publish_up DESC, s.ordering ASC, a.ordering ASC'); break;
			default: $query->order('a.art_publish_up DESC, s.ordering ASC, a.ordering ASC'); break;
		}
		
		return $query;
	}
	
	function getArticles($artids,$secid) {
		$this->artids=$artids;
		$this->secid=$secid;
		return $this->getItems();
	}
	
	function getItems() {
		
		$db =& JFactory::getDBO();
		
		$query = $this->getListQuery();
		$limitstart = $this->getState('list.start');
		$limit = $this->getState('list.limit');
		
		$db->setQuery($query, $limitstart, $limit);
		$items = $db->loadObjectList();
				
		//Get Authors, Cats, Fields
		foreach ($items as &$i) {
			$i->auts=$this->getFieldAuthors($i->art_id,"5");
			
			$qc=$db->getQuery(true);
			$qc->select('c.cat_id,c.cat_title,c.cat_alias');
			$qc->from('#__mams_artcat as ac');
			$qc->join('RIGHT','#__mams_cats AS c ON ac.ac_cat = c.cat_id');
			$qc->where('ac.published >= 1');
			$qc->where('c.published >= 1');
			$qc->where('c.access IN ('.implode(",",$this->alvls).')');
			$qc->where('ac.ac_art = '.$i->art_id);
			$qc->order('ac.ordering ASC');
			$db->setQuery($qc);
			$i->cats=$db->loadObjectList();
			
			if ($i->art_fielddata)
			{
				$registry = new JRegistry;
				$registry->loadString($i->art_fielddata);
				$i->art_fielddata = $registry->toObject();
			}
			
			$i->fields = $this->getArticleListFields($i->art_id);
		}
		
		return $items;
	}
	
	protected function getArticleListFields($artid) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('*');
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
			
			$registry = new JRegistry;
			$registry->loadString($i->params);
			$i->params = $registry->toObject();
		}
			
		return $items;
	}
	
	protected function getFieldAuthors($artid, $fid) {
		$db =& JFactory::getDBO();
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
		$db =& JFactory::getDBO();
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
		$db =& JFactory::getDBO();
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
		$db =& JFactory::getDBO();
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
		$db =& JFactory::getDBO();
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
	
	function getAuthArts($aut) {
		$db =& JFactory::getDBO();
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
	
	function getSecInfo($sec) {
		$db =& JFactory::getDBO();
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
		$db =& JFactory::getDBO();
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
	
	function getAutInfo($aut) {
		$db =& JFactory::getDBO();
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
	

}