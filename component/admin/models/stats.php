<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

class MAMSModelStats extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array();
		}
		parent::__construct($config);
	}
		
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		$cfg = MAMSHelper::getConfig();
		
		// Load the filter state.
		$startdate		= $this->getUserStateFromRequest( $this->context.'.startdate','startdate',date("Y-m-d",strtotime("-1 months") ));
		$enddate		= $this->getUserStateFromRequest( $this->context.'.enddate','enddate',date("Y-m-d") );
		//$filter_sec 	= $this->getUserStateFromRequest( $this->context.'.filter_sec','filter_sec', 0 );
		$filter_type 	= $this->getUserStateFromRequest( $this->context.'.filter_type','filter_type', 'article' );
		
		$this->setState('startdate', $startdate);
		$this->setState('enddate', $enddate);
		//$this->setState('filter_sec', $filter_sec);
		$this->setState('filter_type', $filter_type);
		
		//ContinuEd Link
		if ($cfg->continued) {
			$filter_group = $this->getUserStateFromRequest( $this->context.'.filter.group','filter_group', "" );
			$this->setState('filter.group', $filter_group);
		}
		
		
		// Load the parameters.
		$params = JComponentHelper::getParams('com_mams');
		$this->setState('params', $params);
		
		// List state information.
		parent::populateState('s.mt_time', 'desc');
	}

	protected function getListQuery()
	{
		$cfg = MAMSHelper::getConfig();
		
		// Create a new query object.
		$db = JFactory::getDBO();
		$q = $db->getQuery(true);
		
		$startdate = $this->getState('startdate');
		$enddate = $this->getState('enddate');
		//$filter_sec = $this->getState('filter_sec');
		$filter_type = $this->getState('filter_type');
		
		$q->select('s.*');
		$q->from('#__mams_track as s');
		
		if ($filter_type == 'article') {
			$q->select('a.art_title as item_title');
			$q->select('sec.sec_name as sec_title');
			$q->join('LEFT', '#__mams_articles as a ON s.mt_item = a.art_id');
			$q->join('LEFT', '#__mams_secs as sec ON a.art_sec = sec.sec_id');
			
			$q->where('s.mt_type = "'.$filter_type.'"');
		}
		
		if ($filter_type == 'author' || $filter_type == 'autlist') {
			$q->select('a.auth_name as item_title');
			$q->select('sec.sec_name as sec_title');
			$q->join('LEFT', '#__mams_authors as a ON s.mt_item = a.auth_id');
			$q->join('LEFT', '#__mams_secs as sec ON a.auth_sec = sec.sec_id');
			
			$q->where('s.mt_type = "'.$filter_type.'"');
		}
		
		if ($filter_type == 'seclist') {
			$q->select('a.sec_name as item_title');
			$q->select('"Section List" as sec_title');
			$q->join('LEFT', '#__mams_secs as a ON s.mt_item = a.sec_id');
			
			$q->where('s.mt_type = "'.$filter_type.'"');
		}
		
		if ($filter_type == 'catlist') {
			$q->select('a.cat_title as item_title');
			$q->select('"Category List" as sec_title');
			$q->join('LEFT', '#__mams_cats as a ON s.mt_item = a.cat_id');
			
			$q->where('s.mt_type = "'.$filter_type.'"');
		}
		
		if ($filter_type == 'dload') {
			$q->select('CONCAT(a.dl_lname," - ",a.dl_fname) as item_title');
			$q->select('"Download" as sec_title');
			$q->join('RIGHT', '#__mams_dloads as a ON s.mt_item = a.dl_id');
			$q->where('s.mt_type = "'.$filter_type.'"');
		}
		
		if ($filter_type == 'authors') {
			$q->select('"Authors List" as sec_title');
			$q->where('s.mt_type = "'.$filter_type.'"');
		}
		
		if ($filter_type == 'media') {
			$q->select('m.med_inttitle as item_title');
			$q->select('"Media Played" as sec_title');
			$q->join('LEFT', '#__mams_media as m ON s.mt_item = m.med_id');
			$q->where('s.mt_type = "'.$filter_type.'"');
		}
		
		
		
		$q->select('u.name as users_name, u.email as users_email, u.username as users_username');
		$q->join('LEFT', '#__users as u ON s.mt_user = u.id');
		
		
		if ($cfg->except_iplist) {
			$iplist = explode(",",$cfg->except_iplist);
			$q->where('s.mt_ipaddr NOT IN ("'.implode('","',$iplist).'")');
		}
		
		if ($cfg->except_userlist) {
			$userlist = explode(",",$cfg->except_userlist);
			$q->where('s.mt_user NOT IN ("'.implode('","',$userlist).'")');
		}
		
		if ($cfg->continued) {
			$q->select("ceug.ug_name AS UserGroup");
			$q->join('LEFT', '#__ce_usergroup as ceg ON s.mt_user = ceg.userg_user');
			$q->join('LEFT', '#__ce_ugroups as ceug ON ceg.userg_group = ceug.ug_id');
			// Filter by section.
			if ($ugroup = $this->getState('filter.group')) {
				$q->where('ceg.userg_group = '.(int) $ugroup);
			}
		}
		
		if ($cfg->mue) {
			$q->select("mueug.ug_name AS UserGroup");
			$q->join('LEFT', '#__mue_usergroup as mueg ON s.mt_user = mueg.userg_user');
			$q->join('LEFT', '#__mue_ugroups as mueug ON mueg.userg_group = mueug.ug_id');
			// Filter by section.
			if ($ugroup = $this->getState('filter.group')) {
				$q->where('mueg.userg_group = '.(int) $ugroup);
			}
		}
		
		$q->where('date(s.mt_time) BETWEEN "'.$startdate.'" AND "'.$enddate.'"');
		
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		
		$q->order($db->escape($orderCol.' '.$orderDirn));

		return $q;
	}
	
	public function getItemsCSV()
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
		$items = $this->_getList($query);
		
		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		// Add the items to the internal cache.
		$this->cache[$store] = $items;
		
		return $this->cache[$store];
	}

	public function getUserGroups() {
		$cfg = MAMSHelper::getConfig();
		if ($cfg->continued) { $tbl = '#__ce_ugroups'; }
		else if ($cfg->mue) { $tbl = '#__mue_ugroups'; }
		$q  = 'SELECT ug.ug_name as text,ug.ug_id as value FROM '.$tbl.' as ug';
		$q .= ' ORDER BY ug.ug_name';
		$this->_db->setQuery($q);
		$glist = $this->_db->loadObjectList();
		$glist[]->text='-- All --';
		return $glist;
	}
	
}
