<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class MAMSModelArticle extends JModelLegacy
{
	protected $alvls = Array();
	
	function __construct($config = array()) {
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$this->ulvls = $user->getAuthorisedViewLevels();
		$this->alvls = array_merge($this->ulvls,$cfg->reggroup);

		parent::__construct($config);
	}
	
	function getArticleSec($artid) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.art_sec');
		$query->from('#__mams_articles AS a');
		$query->where('a.art_id = '.$artid);
		$query->where('a.state >= 1');
		$db->setQuery($query);
		return $db->loadResult();
	}

	function getArticleAccessDetails($artid) {
		$app = JFactory::getApplication('site');
		$cfg = MAMSHelper::getConfig();
		$user = JFactory::getUser();

		$accessDetalis = new stdClass();
		$accessDetalis->exists = false;
		$accessDetalis->canAccess = false;
		$accessDetalis->hasPreview = false;

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.access,a.params,a.art_publish_up,a.art_publish_down,a.state');
		$query->from('#__mams_articles AS a');
		$query->where('a.art_id = '.$artid);
		$query->where('a.state >= 1');
		if (!in_array($cfg->ovgroup,$this->alvls)) { $query->where('a.art_publish_up <= NOW()'); $query->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }
		$query->order('a.art_publish_up DESC');
		$db->setQuery($query);
		$item = $db->loadObject();

		if (!$item) {
			return $accessDetalis;
		}

		$accessDetalis->exists = true;

		if (in_array($item->access,$user->getAuthorisedViewLevels())) {
			$accessDetalis->canAccess = true;
		}

		//Load up the Params
		$registry = new JRegistry;
		$registry->loadString($item->params);
		$item->params = $registry;

		// Merge menu item params with item params, item params take precedence
		$params = $app->getParams();
		$params->merge($item->params);
		$item->params = $params;

		if ($item->params->get("show_preview",0)) {
			$accessDetalis->hasPreview = true;
		}

		return $accessDetalis;
	}
	
	function getArticle($artid, $preview=false) {
		$app = JFactory::getApplication('site');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
		$query->from('#__mams_articles AS a');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.art_id = '.$artid);
		$query->where('a.state >= 1');
		if (!$preview) $query->where('a.access IN ('.implode(",",$this->alvls).')');
		if (!in_array($cfg->ovgroup,$this->alvls)) { $query->where('a.art_publish_up <= NOW()'); $query->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }
		$query->order('a.art_publish_up DESC');
		$db->setQuery($query);
		$item = $db->loadObject();
		
		if (!$item) return false;

		// if preview, set article content to preview content
		if ($preview) {
			$item->art_content = $item->art_preview;
		}
		
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

		// Add hit
		$qhit = $db->getQuery(true);
		$qhit->update('#__mams_articles');
		$qhit->set('art_hits = art_hits + 1');
		$qhit->where('art_id = '.$artid);
		$db->setQuery($qhit);
		$db->execute();
		
		//Get Article Drilldowns
		$item->cats=$this->getArticleCats($item->art_id);
		$item->tags=$this->getArticleTags($item->art_id);
		$item->auts=$this->getFieldAuthors($item->art_id,5);
		
		//Additional Fields
		if ($item->art_fielddata)
		{
			$registry = new JRegistry;
			$registry->loadString($item->art_fielddata);
			$item->art_fielddata = $registry->toObject();
		}
			
		$item->fields = $this->getArticleFields($item->art_id,$preview);
		
		return $item;
	}
	
	protected function getArticleFields($artid, $preview = false) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('f.*');
		$query->from("#__mams_article_fields as f");
		$query->select('g.group_title,g.group_show_title,g.group_name');
		$query->join('LEFT', '#__mams_article_fieldgroups AS g ON g.group_id = f.field_group');
		$query->where('f.published >= 1');
		$query->where('f.access IN ('.implode(",",$this->ulvls).')');
		$query->where('g.published >= 1');
		$query->where('g.access IN ('.implode(",",$this->ulvls).')');
		if ($preview) $query->where('f.field_show_preview = 1');
		else $query->where('f.field_show_page = 1');
		$query->order('f.ordering ASC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		foreach ($items as &$i) {
			switch ($i->field_type) {
				case "auths": $i->data = $this->getFieldAuthors($artid,$i->field_id); break;
				case "media": $i->data = $this->getFieldMedia($artid,$i->field_id); break;
				case "dloads": $i->data = $this->getFieldDownloads($artid,$i->field_id); break;
				case "links": $i->data = $this->getFieldLinks($artid,$i->field_id); break;
				case "images": $i->data = $this->getFieldImages($artid,$i->field_id); break;
			}
			
			$registry = new JRegistry;
			$registry->loadString($i->params);
			$i->params = $registry->toObject();
		}
			
		return $items;
	}
	
	protected function getFieldAuthors($artid, $fid) {
		$db = JFactory::getDBO();
		$qa=$db->getQuery(true);
		$qa->select('a.*');
		$qa->from('#__mams_artauth as aa');
		$qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
		$qa->where('aa.published >= 1');
		$qa->where('a.published >= 1');
		$qa->where('a.access IN ('.implode(",",$this->ulvls).')');
		$qa->where('aa.aa_art = '.$artid);
		$qa->where('aa.aa_field = '.$fid);
		$qa->order('aa.ordering ASC');
		$db->setQuery($qa);
		return $db->loadObjectList();
	}
	
	protected function getFieldMedia($artid, $fid) {		
		$db = JFactory::getDBO();
		$qa=$db->getQuery(true);
		$qa->select('m.*');
		$qa->from('#__mams_artmed as am');
		$qa->join('RIGHT','#__mams_media AS m ON am.am_media = m.med_id');
		$qa->where('am.published >= 1');
		$qa->where('m.published >= 1');
		$qa->where('m.access IN ('.implode(",",$this->ulvls).')');
		$qa->where('am.am_art = '.$artid);
		$qa->where('am.am_field = '.$fid);
		$qa->order('am.ordering ASC');
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
		$qa->where('d.access IN ('.implode(",",$this->ulvls).')');
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
		$qa->where('l.access IN ('.implode(",",$this->ulvls).')');
		$qa->where('al.al_art = '.$artid);
		$qa->where('al.al_field = '.$fid);
		$qa->order('al.ordering ASC');
		$db->setQuery($qa);
		return $db->loadObjectList();
	}
	
	protected function getFieldImages($artid, $fid) {		
		$db = JFactory::getDBO();
		$qa=$db->getQuery(true);
		$qa->select('i.*');
		$qa->from('#__mams_artimg as ai');
		$qa->join('RIGHT','#__mams_images AS i ON ai.ai_image = i.img_id');
		$qa->where('ai.published >= 1');
		$qa->where('i.published >= 1');
		$qa->where('i.access IN ('.implode(",",$this->ulvls).')');
		$qa->where('ai.ai_art = '.$artid);
		$qa->where('ai.ai_field = '.$fid);
		$qa->order('ai.ordering ASC');
		$db->setQuery($qa); 
		return $db->loadObjectList();
	}
	
	protected function getArticleCats($artid) {
		$db = JFactory::getDBO();
		$qa=$db->getQuery(true);
		$qa->select('c.*');
		$qa->from('#__mams_artcat as ac');
		$qa->join('RIGHT','#__mams_cats AS c ON ac.ac_cat = c.cat_id');
		$qa->where('ac.published >= 1');
		$qa->where('c.published >= 1');
		$qa->where('c.access IN ('.implode(",",$this->ulvls).')');
		$qa->where('ac.ac_art = '.$artid);
		$qa->order('ac.ordering ASC');
		$db->setQuery($qa);
		return $db->loadObjectList();
	}

	protected function getArticleTags($artid) {
		$db = JFactory::getDBO();
		$qc = $db->getQuery( true );
		$qc->select( 't.tag_id,t.tag_title,t.tag_alias,t.tag_icon' );
		$qc->from( '#__mams_arttag as at' );
		$qc->join( 'RIGHT', '#__mams_tags AS t ON at.at_tag = t.tag_id' );
		$qc->where( 'at.published >= 1' );
		$qc->where( 't.published >= 1' );
		$qc->where( 't.access IN (' . implode( ",", $this->ulvls ) . ')' );
		$qc->where( 'at.at_art = ' . $artid );
		$qc->order( 'at.ordering ASC' );
		$db->setQuery( $qc );
		return $db->loadObjectList();
	}

	function getRelated($article,$cats,$auts,$secid) {
		$app = JFactory::getApplication('site');
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		$relatedids = Array();

		$relatedBy = $cfg->relatedby;
		if (!$relatedBy) $relatedBy = 'both';
	
		if ($relatedBy == 'both' || $relatedBy == 'category') foreach ($cats as $c) { $relatedids=array_merge($relatedids,$this->getCatArts($article->art_id, $c->cat_id)); }
		if ($relatedBy == 'both' || $relatedBy == 'author') foreach ($auts as $a) { $relatedids=array_merge($relatedids,$this->getAuthArts($article->art_id, $a->auth_id));}
		$relatedids = array_unique($relatedids);	
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		if ($relatedids) {
			$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
			$query->from('#__mams_articles AS a');
			$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
			$query->where('a.art_id IN ('.implode(",",$relatedids).')');
			if ($cfg->related_sec) $query->where('a.art_sec = '.$secid);
			$query->where('a.state >= 1');
			if (!$article->params->get('related_by_feataccess',0)) $query->where('a.access IN ('.implode(",",$this->alvls).')');
			else $query->where('a.feataccess IN ('.implode(",",$this->alvls).')');
			if (!in_array($cfg->ovgroup,$this->alvls)) { $query->where('a.art_publish_up <= NOW()'); $query->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }
			$query->order('a.art_publish_up DESC, a.ordering ASC');
			$limit = (int)$cfg->num_related;
			$db->setQuery($query,0,$limit);
			$items = $db->loadObjectList();
			
			//Get Authors & Cats
			foreach ($items as &$i) {

				//Load up the Params
				$registry = new JRegistry;
				$registry->loadString($i->params);
				$i->params = $registry;

				// Merge menu item params with item params, item params take precedence
				$params = clone $app->getParams();
				$params->merge($i->params);
				$i->params = $params;

				$i->auts=$this->getFieldAuthors($i->art_id,5);
				$i->cats=$this->getArticleCats($i->art_id);
				$i->tags=$this->getArticleTags($i->art_id);
			}
		} else {
			return false;
		}
		
		return $items;
	
	}
	
	function getCatArts($art, $cat) {
		$db = JFactory::getDBO();
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
		$db = JFactory::getDBO();
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