<?php


// no direct access
defined('_JEXEC') or die;

class modMAMSCatHelper
{
	static function getFeatured($params)
	{
		$db		= JFactory::getDbo();
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$alvls = Array();
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
		
		$qcat = $db->getQuery(true);
		$qcat->select('ac.ac_art');
		$qcat->from('#__mams_artcat AS ac');
		$qcat->where('ac.ac_cat = '.(int)$params->get('catid'));
		$qcat->where('ac.published >= 1');
		$db->setQuery($qcat);
		$catids = $db->loadResultArray(0);
		
		
		$query	= $db->getQuery(true);

		$query->select('a.*,s.*');
		$query->from('#__mams_articles as a');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.art_id IN ('.implode(",",$catids).')');
		$query->where('a.access IN ('.implode(",",$alvls).')');
		$query->where('a.published >= 1');
		if (!in_array($cfg->ovgroup,$alvls)) $query->where('a.art_published <= NOW()');
		$query->order('a.art_published DESC, s.ordering ASC, a.ordering ASC');
		$db->setQuery($query,0,$params->get('count',5));
		$items = $db->loadObjectList();
		
		
		
		
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

}
