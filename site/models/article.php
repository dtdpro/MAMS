<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class MAMSModelArticle extends JModelLegacy
{
	function getArticleSec($artid) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.art_sec');
		$query->from('#__mams_articles AS a');
		$query->where('a.art_id = '.$artid);
		$query->where('a.published >= 1');
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	function getArticle($artid) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
		
		$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
		$query->from('#__mams_articles AS a');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.art_id = '.$artid);
		$query->where('a.published >= 1');
		$query->where('a.access IN ('.implode(",",$alvls).')');
		if (!in_array($cfg->ovgroup,$alvls)) $query->where('a.art_published <= NOW()');
		$query->order('a.art_published DESC');
		$db->setQuery($query);
		$item = $db->loadObject();
		
		if (!$item) return 0;
		
		$qhit = $db->getQuery(true);
		$qhit->update('#__mams_articles');
		$qhit->set('art_hits = art_hits + 1');
		$qhit->where('art_id = '.$artid);
		$db->setQuery($qhit);
		$db->query();
		
		//Get Authors
		$qa=$db->getQuery(true);
		$qa->select('a.auth_id,a.auth_name,a.auth_alias,a.auth_credentials,a.auth_sec');
		$qa->from('#__mams_artauth as aa');
		$qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
		$qa->where('aa.published >= 1');
		$qa->where('a.published >= 1');
		$qa->where('a.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
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
		
		//Get Links
		$qm=$db->getQuery(true);
		$qm->select('l.*');
		$qm->from('#__mams_artlinks as al');
		$qm->join('RIGHT','#__mams_links AS l ON al.al_link = l.link_id');
		$qm->where('al.published >= 1');
		$qm->where('l.published >= 1');
		$qm->where('l.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$qm->where('al.al_art = '.$item->art_id);
		$qm->order('al.ordering ASC');
		$db->setQuery($qm);
		$item->links=$db->loadObjectList();
		
		return $item;
	}
	
	function getRelatedByCat($art,$cats,$secid) {
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		$relatedids = Array();
	
		foreach ($cats as $c) { $relatedids=array_merge($relatedids,$this->getCatArts($art, $c->cat_id)); }
		$relatedids = array_unique($relatedids);	
		
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
		
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		
		if ($relatedids) {
			$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
			$query->from('#__mams_articles AS a');
			$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
			$query->where('a.art_id IN ('.implode(",",$relatedids).')');
			$query->where('a.art_sec = '.$secid);
			$query->where('a.published >= 1');
			$query->where('a.access IN ('.implode(",",$alvls).')');
			if (!in_array($cfg->ovgroup,$alvls)) $query->where('a.art_published <= NOW()');
			$query->order('a.art_published DESC, a.ordering ASC');
			$limit = (int)$cfg->num_related;
			$db->setQuery($query,0,$limit);
			$items = $db->loadObjectList();
			
			//Get Authors
			foreach ($items as &$i) {
				$qa=$db->getQuery(true);
				$qa->select('a.auth_id,a.auth_name,a.auth_alias');
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
		}
		
		return $items;
	
	}
	
	function getRelatedByAut($art,$auts,$secid) {
		$relatedids = Array();
		$cfg = MAMSHelper::getConfig();
		$user = JFactory::getUser();
	
		foreach ($auts as $a) { $relatedids=array_merge($relatedids,$this->getAuthArts($art, $a->auth_id));}
		$relatedids = array_unique($relatedids);	
		
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
		
		if ($relatedids) {
			$db =& JFactory::getDBO();
			$query = $db->getQuery(true);
			
			$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
			$query->from('#__mams_articles AS a');
			$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
			$query->where('a.art_id IN ('.implode(",",$relatedids).')');
			$query->where('a.art_sec = '.$secid);
			$query->where('a.published >= 1');
			$query->where('a.access IN ('.implode(",",$alvls).')');
			if (!in_array($cfg->ovgroup,$alvls)) $query->where('a.art_published <= NOW()');
			$query->order('a.art_published DESC, a.ordering ASC');
			$limit = (int)$cfg->num_related;
			$db->setQuery($query,0,$limit);
			$items = $db->loadObjectList();
			
			//Get Authors
			foreach ($items as &$i) {
				$qa=$db->getQuery(true);
				$qa->select('a.auth_id,a.auth_name,a.auth_alias');
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
		}
		return $items;
	
	}
	
	function getCatArts($art, $cat) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('ac.ac_art');
		$query->from('#__mams_artcat AS ac');
		$query->where('ac.ac_cat = '.(int)$cat);
		$query->where('ac.published >= 1');
		$query->where('ac.ac_art != '.$art);
		$db->setQuery($query);
		$items = $db->loadColumn(0);
		return $items;
	}
	
	function getAuthArts($art, $aut) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		
		$query->select('aa.aa_art');
		$query->from('#__mams_artauth AS aa');
		$query->where('aa.aa_auth = '.(int)$aut);
		$query->where('aa.published >= 1');
		$query->where('aa.aa_art != '.$art);
		$db->setQuery($query);
		$items = $db->loadColumn(0);
		return $items;
	}
	
	

}