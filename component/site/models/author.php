<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class MAMSModelAuthor extends JModelLegacy
{
	function getAuthor($autid) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('a.*');
		$query->from('#__mams_authors AS a');
		$query->where('a.auth_id = '.$autid);
		$query->where('a.published >= 1');
		$query->where('a.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$db->setQuery($query); 
		$item = $db->loadObject();
		
		return $item;
	}
	
	function getAuthorList($secid) {
		$db =& JFactory::getDBO();
		$qsec = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$qsec->select('sec_id, sec_name, sec_alias');
		$qsec->from('#__mams_secs');
		$qsec->where('sec_type = "author"');
		if ($secid) $qsec->where('sec_id = '.$secid);
		$qsec->order('ordering ASC');
		$db->setQuery($qsec);
		$secs = $db->loadObjectList();
		
		foreach ($secs as &$s) {
			$query = $db->getQuery(true);
			$query->select('a.*');
			$query->from('#__mams_authors AS a');
			$query->where('a.auth_sec = '.$s->sec_id);
			$query->where('a.published >= 1');
			$query->where('a.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
			$query->order('a.ordering');
			$db->setQuery($query);
			$s->authors = $db->loadObjectList();
		}
		return $secs;
	}
	
	function getPublished($autid) {
		$pubedids=$this->getAuthArts($autid);	
		if (!$pubedids) return false;
		
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
		
		$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
		$query->from('#__mams_articles AS a');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.art_id IN ('.implode(",",$pubedids).')');
		$query->where('a.state >= 1');
		$query->where('a.access IN ('.implode(",",$alvls).')');
		if (!in_array($cfg->ovgroup,$alvls)) { $query->where('a.art_publish_up <= NOW()'); $query->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }
		$query->order('a.art_publish_up DESC, s.ordering ASC, a.ordering ASC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		//Get Authors
		foreach ($items as &$i) {
			$qa=$db->getQuery(true);
			$qa->select('a.auth_id,a.auth_fname,a.auth_mi,a.auth_lname,a.auth_titles,a.auth_alias,a.auth_sec');
			$qa->from('#__mams_artauth as aa');
			$qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
			$qa->where('aa.published >= 1');
			$qa->where('a.published >= 1');
			$qa->where('a.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
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
			$qc->where('c.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
			$qc->where('ac.ac_art = '.$i->art_id);
			$qc->order('ac.ordering ASC');
			$db->setQuery($qc);
			$i->cats=$db->loadObjectList();
		}
		
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
		$items = $db->loadColumn(0);
		return $items;
	}
	
	function getAuthCourses($aut) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
	
		$query->select('ca.ca_course');
		$query->from('#__mcme_courseauth AS ca');
		$query->where('ca.ca_auth = '.(int)$aut);
		$query->where('ca.published >= 1');
		$db->setQuery($query);
		$pubedids = $db->loadColumn(0);
		
		if (!$pubedids) return false;
		
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
		
		$query->select('a.*,c.sec_id,c.sec_title');
		$query->from('#__mcme_courses AS a');
		$query->join('RIGHT','#__mcme_secs AS c ON c.sec_id = a.course_sec');
		$query->where('a.course_id IN ('.implode(",",$pubedids).')');
		$query->where('a.published >= 1');
		$query->where('a.access IN ('.implode(",",$alvls).')');
		if (!in_array($cfg->ovgroup,$alvls)) $query->where('a.course_startdate <= NOW()');
		$query->order('a.course_startdate DESC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		//Get Authors
		foreach ($items as &$i) {
			$qa=$db->getQuery(true);
			$qa->select('a.auth_id,a.auth_fname,a.auth_mi,a.auth_lname,a.auth_titles,a.auth_alias,a.auth_sec');
			$qa->from('#__ce_courseauth as ca');
			$qa->join('RIGHT','#__mams_authors AS a ON ca.ca_auth = a.auth_id');
			$qa->where('ca.published >= 1');
			$qa->where('a.published >= 1');
			$qa->where('a.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
			$qa->where('ca.ca_course = '.$i->course_id);
			$qa->order('ca.ordering ASC');
			$db->setQuery($qa);
			$i->auts=$db->loadObjectList();
		}

		//Get Cats
		foreach ($items as &$i) {
			$qc=$db->getQuery(true);
			$qc->select('c.cat_id,c.cat_title,c.cat_alias');
			$qc->from('#__mcme_coursecat as cc');
			$qc->join('RIGHT','#__mams_cats AS c ON cc.cc_cat = c.cat_id');
			$qc->where('cc.published >= 1');
			$qc->where('c.published >= 1');
			$qc->where('c.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
			$qc->where('cc.cc_course = '.$i->course_id);
			$qc->order('cc.ordering ASC');
			$db->setQuery($qc);
			$i->cats=$db->loadObjectList();
		}
		
		return $items;
		
		
		
	}
}