<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

class MAMSModelArtList extends JModelList
{
	protected $artids = Array();
	protected $secid = 0;
	protected $params = null;
	
	function populateState($ordering = null, $direction = null)
	{
		// Initiliase variables.
		$app	= JFactory::getApplication('site');
		
		// Load the parameters. Merge Global and Menu Item params into new object
		$this->params = $app->getParams();
		$this->setState('params', $params);
		
		// pagination
		$this->setState('list.start', JRequest::getVar('limitstart', 0, '', 'int'));
		$limit = $this->params->get("items_page",10);
		$this->setState('list.limit', $limit);
	}
	
	function getListQuery()
	{
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$alvls = Array();
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
		
		$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
		$query->from('#__mams_articles AS a');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.art_id IN ('.implode(",",$this->artids).')');
		if ($this->secid) $query->where('a.art_sec = '.$this->secid);
		$query->where('a.published >= 1');
		$query->where('a.access IN ('.implode(",",$alvls).')');
		if (!in_array($cfg->ovgroup,$alvls)) $query->where('a.art_published <= NOW()');
		switch ($this->params->get("orderby","pubdsc")) {
			case "titasc": $query->order('a.art_title ASC'); break;
			case "titdsc": $query->order('a.art_title DESC'); break;
			case "pubasc": $query->order('a.art_published ASC, s.ordering ASC, a.ordering ASC'); break;
			case "pubdsc": $query->order('a.art_published DESC, s.ordering ASC, a.ordering ASC'); break;
			default: $query->order('a.art_published DESC, s.ordering ASC, a.ordering ASC'); break;
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
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$query = $this->getListQuery();
		$limitstart = $this->getState('list.start');
		$limit = $this->getState('list.limit');
		
		$db->setQuery($query, $limitstart, $limit);
		$items = $db->loadObjectList();
		
		$alvls = Array();
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
				
		//Get Authors
		foreach ($items as &$i) {
			$qa=$db->getQuery(true);
			$qa->select('a.auth_id,a.auth_name,a.auth_alias,a.auth_sec');
			$qa->from('#__mams_artauth as aa');
			$qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
			$qa->where('aa.published >= 1');
			$qa->where('a.published >= 1');
			$qa->where('a.access IN ('.implode(",",$alvls).')');
			$qa->where('aa.aa_art = '.$i->art_id);
			$qa->order('aa.ordering ASC');
			$db->setQuery($qa);
			$i->auts=$db->loadObjectList();
		}
		
		//Get Cats
		foreach ($items as &$i) {
			$qc=$db->getQuery(true);
			$qc->select('c.cat_id,c.cat_title,c.cat_alias');
			$qc->from('#__mams_artcat as ac');
			$qc->join('RIGHT','#__mams_cats AS c ON ac.ac_cat = c.cat_id');
			$qc->where('ac.published >= 1');
			$qc->where('c.published >= 1');
			$qc->where('c.access IN ('.implode(",",$alvls).')');
			$qc->where('ac.ac_art = '.$i->art_id);
			$qc->order('ac.ordering ASC');
			$db->setQuery($qc);
			$i->cats=$db->loadObjectList();
		}
		
		return $items;
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
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$alvls = Array();
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
		
		$query->select('a.art_id');
		$query->from('#__mams_articles AS a');
		$query->where('a.art_sec = '.(int)$sec);
		$query->where('a.published >= 1');
		$query->where('a.access IN ('.implode(",",$alvls).')');
		$db->setQuery($query);
		$items = $db->loadResultArray(0);
		return $items;
	}
	
	function getCatArts($cat) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('ac.ac_art');
		$query->from('#__mams_artcat AS ac');
		$query->where('ac.ac_cat = '.(int)$cat);
		$query->where('ac.published >= 1');
		$db->setQuery($query);
		$items = $db->loadResultArray(0);
		return $items;
	}
	
	function getAuthArts($aut) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('aa.aa_art');
		$query->from('#__mams_artauth AS aa');
		$query->where('aa.aa_auth = '.(int)$aut);
		$query->where('aa.published >= 1');
		$db->setQuery($query);
		$items = $db->loadResultArray(0);
		return $items;
	}
	
	function getSecInfo($sec) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('s.*');
		$query->from('#__mams_secs AS s');
		$query->where('s.sec_id = '.(int)$sec);
		$query->where('s.published >= 1');
		$query->where('s.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$db->setQuery($query);
		$info = $db->loadObject();
		return $info;
	}
	
	function getCatInfo($cat) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('c.*');
		$query->from('#__mams_cats AS c');
		$query->where('c.cat_id = '.(int)$cat);
		$query->where('c.published >= 1');
		$query->where('c.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$db->setQuery($query);
		$info = $db->loadObject();
		return $info;
	}
	
	function getAutInfo($aut) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('a.*');
		$query->from('#__mams_authors AS a');
		$query->where('a.auth_id = '.(int)$aut);
		$query->where('a.published >= 1');
		$query->where('a.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$db->setQuery($query);
		$info = $db->loadObject();
		return $info;
	}
	

}