<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class MAMSModelArticle extends JModel
{
	function getArticle($artid) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
		$query->from('#__mams_articles AS a');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.art_id = '.$artid);
		$query->where('a.published >= 1');
		$query->where('a.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$query->where('a.art_published <= NOW()');
		$query->order('a.art_published DESC');
		$db->setQuery($query);
		$item = $db->loadObject();
		
		//Get Authors
		$qa=$db->getQuery(true);
		$qa->select('a.auth_id,a.auth_name,a.auth_alias,a.auth_credentials');
		$qa->from('#__mams_artauth as aa');
		$qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
		$qa->where('aa.published >= 1');
		$qa->where('a.published >= 1');
		$qa->where('aa.aa_art = '.$item->art_id);
		$qa->order('aa.ordering ASC');
		$db->setQuery($qa);
		$item->auts=$db->loadObjectList();
		
		//Get Cats
		$qc=$db->getQuery(true);
		$qc->select('c.cat_id,c.cat_title,c.cat_alias');
		$qc->from('#__mams_artcat as ac');
		$qc->join('RIGHT','#__mams_cats AS c ON ac.ac_cat = c.cat_id');
		$qc->where('ac.published >= 1');
		$qc->where('c.published >= 1');
		$qc->where('c.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$qc->where('ac.ac_art = '.$item->art_id);
		$qc->order('ac.ordering ASC');
		$db->setQuery($qc);
		$item->cats=$db->loadObjectList();
		
		//Get DLoads
		$qd=$db->getQuery(true);
		$qd->select('d.*');
		$qd->from('#__mams_artdl as ad');
		$qd->join('RIGHT','#__mams_dloads AS d ON ad.ad_dload = d.dl_id');
		$qd->where('ad.published >= 1');
		$qd->where('d.published >= 1');
		$qd->where('d.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$qd->where('ad.ad_art = '.$item->art_id);
		$qd->order('ad.ordering ASC');
		$db->setQuery($qd);
		$item->dloads=$db->loadObjectList();
		
		//Get Media
		$qm=$db->getQuery(true);
		$qm->select('m.*');
		$qm->from('#__mams_artmed as am');
		$qm->join('RIGHT','#__mams_media AS m ON am.am_media = m.med_id');
		$qm->where('am.published >= 1');
		$qm->where('m.published >= 1');
		$qm->where('m.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$qm->where('am.am_art = '.$item->art_id);
		$qm->order('am.ordering ASC');
		$db->setQuery($qm);
		$item->media=$db->loadObjectList();
		
		return $item;
	}
	
	function getRelated($cats,$auts,$secid) {
		$relatedids = Array();
	
		foreach ($cats as $c) { $relatedids=array_merge($relatedids,$this->getCatArts($c->cat_id)); }
		foreach ($auts as $a) { $relatedids=array_merge($relatedids,$this->getAuthArts($a->auth_id));}
		$relatedids = array_unique($relatedids);	
		
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
		$query->from('#__mams_articles AS a');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.art_id IN ('.implode(",",$relatedids).')');
		$query->where('a.art_sec = '.$secid);
		$query->where('a.published >= 1');
		$query->where('a.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$query->where('a.art_published <= NOW()');
		$query->order('a.art_published DESC');
		$query->limit(10);
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		//Get Authors
		foreach ($items as &$i) {
			$qa=$db->getQuery(true);
			$qa->select('a.auth_id,a.auth_name,a.auth_alias');
			$qa->from('#__mams_artauth as aa');
			$qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
			$qa->where('aa.published >= 1');
			$qa->where('a.published >= 1');
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
}