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
		$artids = $db->loadColumn(0);
		
		$query	= $db->getQuery(true);

		$query->select('a.*,s.*');
		$query->from('#__mams_articles as a');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.art_id IN ('.implode(",",$artids).')');
		$query->where('a.access IN ('.implode(",",$alvls).')');
		$query->where('a.state >= 1');
		if (!in_array($cfg->ovgroup,$alvls)) { $query->where('a.art_publish_up <= NOW()'); $query->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }
		$query->order('a.art_publish_up DESC, s.ordering ASC, a.ordering ASC');
		$db->setQuery($query,0,$params->get('count',5));
		$items = $db->loadObjectList();
		
		//Get Authors
		foreach ($items as &$i) {
			$qa=$db->getQuery(true);
			$qa->select('a.auth_id,a.auth_fname,a.auth_mi,a.auth_lname,a.auth_titles,a.auth_alias,a.auth_sec');
			$qa->from('#__mams_artauth as aa');
			$qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
			$qa->where('aa.published >= 1');
			$qa->where('a.published >= 1');
			$qa->where('a.access IN ('.implode(",",$alvls).')');
			$qa->where('aa.aa_art = '.$i->art_id);
			$qa->where('aa.aa_field = 5');
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
