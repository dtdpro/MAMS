<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class MAMSModelArticle extends JModelItem
{
	protected $alvls = Array();
	
	function __construct($config = array()) {
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$this->alvls = $user->getAuthorisedViewLevels();
		$this->alvls = array_merge($this->alvls,$cfg->reggroup);

		parent::__construct($config);
	}
	
	function getArticleSec($artid) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.art_sec');
		$query->from('#__mams_articles AS a');
		$query->where('a.art_id = '.$artid);
		$query->where('a.state >= 1');
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	function getArticle($artid) {
		$app = JFactory::getApplication('site');
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
		$query->from('#__mams_articles AS a');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.art_id = '.$artid);
		$query->where('a.state >= 1');
		$query->where('a.access IN ('.implode(",",$this->alvls).')');
		if (!in_array($cfg->ovgroup,$this->alvls)) { $query->where('a.art_publish_up <= NOW()'); $query->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }
		$query->order('a.art_publish_up DESC');
		$db->setQuery($query);
		$item = $db->loadObject();
		
		//Load up the Params
		$registry = new JRegistry;
		$registry->loadString($item->params);
		$item->params = $registry;
		
		// Merge menu item params with item params, item params take precedence
		$params = $app->getParams();
		$params->merge($item->params);
		$item->params = $params;
		
		//Metadata
		$registry = new JRegistry;
		$registry->loadString($item->metadata);
		$item->metadata = $registry;
		
		if (!$item) return 0;
		
		$qhit = $db->getQuery(true);
		$qhit->update('#__mams_articles');
		$qhit->set('art_hits = art_hits + 1');
		$qhit->where('art_id = '.$artid);
		$db->setQuery($qhit);
		$db->query();
		
		//Get Article Drilldowns
		$item->cats=$this->getArticleCats($item->art_id);
		$item->auts=$this->getFieldAuthors($item->art_id,5);
		$item->dloads=$this->getFieldDownloads($item->art_id,7);
		$item->media=$this->getFieldMedia($item->art_id,6);
		$item->links=$this->getFieldLinks($item->art_id,8);
		
		//Additional Fields
		if ($item->art_fielddata)
		{
			$registry = new JRegistry;
			$registry->loadString($item->art_fielddata);
			$item->art_fielddata = $registry->toObject();
		}
			
		$item->fields = $this->getArticleFields($item->art_id);
		
		return $item;
	}
	
	protected function getArticleFields($artid) {
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
		$query->where('f.field_show_page = 1');
		$query->order('f.ordering ASC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		foreach ($items as &$i) {
			switch ($i->field_type) {
				case "auths": $i->data = $this->getFieldAuthors($artid,$i->field_id); break;
				case "media": $i->data = $this->getFieldMedia($artid,$i->field_id); break;
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
		$qa->select('a.*');
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
	
	protected function getFieldMedia($artid, $fid) {		
		$db =& JFactory::getDBO();
		$qa=$db->getQuery(true);
		$qa->select('m.*');
		$qa->from('#__mams_artmed as am');
		$qa->join('RIGHT','#__mams_media AS m ON am.am_media = m.med_id');
		$qa->where('am.published >= 1');
		$qa->where('m.published >= 1');
		$qa->where('m.access IN ('.implode(",",$this->alvls).')');
		$qa->where('am.am_art = '.$artid);
		$qa->where('am.am_field = '.$fid);
		$qa->order('am.ordering ASC');
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
	
	protected function getArticleCats($artid) {
		$db =& JFactory::getDBO();
		$qa=$db->getQuery(true);
		$qa->select('c.*');
		$qa->from('#__mams_artcat as ac');
		$qa->join('RIGHT','#__mams_cats AS c ON ac.ac_cat = c.cat_id');
		$qa->where('ac.published >= 1');
		$qa->where('c.published >= 1');
		$qa->where('c.access IN ('.implode(",",$this->alvls).')');
		$qa->where('ac.ac_art = '.$artid);
		$qa->order('ac.ordering ASC');
		$db->setQuery($qa);
		return $db->loadObjectList();
	}
	
	function getRelated($art,$cats,$auts,$secid) {
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		$relatedids = Array();
	
		foreach ($cats as $c) { $relatedids=array_merge($relatedids,$this->getCatArts($art, $c->cat_id)); }
		foreach ($auts as $a) { $relatedids=array_merge($relatedids,$this->getAuthArts($art, $a->auth_id));}
		$relatedids = array_unique($relatedids);	
		
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		
		if ($relatedids) {
			$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
			$query->from('#__mams_articles AS a');
			$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
			$query->where('a.art_id IN ('.implode(",",$relatedids).')');
			$query->where('a.art_sec = '.$secid);
			$query->where('a.state >= 1');
			$query->where('a.access IN ('.implode(",",$this->alvls).')');
			if (!in_array($cfg->ovgroup,$this->alvls)) { $query->where('a.art_publish_up <= NOW()'); $query->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }
			$query->order('a.art_publish_up DESC, a.ordering ASC');
			$limit = (int)$cfg->num_related;
			$db->setQuery($query,0,$limit);
			$items = $db->loadObjectList();
			
			//Get Authors & Cats
			foreach ($items as &$i) {
				$i->auts=$this->getFieldAuthors($i->art_id,5);
				$i->cats=$this->getArticleCats($i->art_id);
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