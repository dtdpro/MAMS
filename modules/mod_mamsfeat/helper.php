<?php


// no direct access
defined('_JEXEC') or die;

class modMAMSFeatHelper
{
	static function getFeatured($params)
	{
		$db		= JFactory::getDbo();
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$alvls = Array();
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
		
		$query	= $db->getQuery(true);

		$query->select('f.*,a.*,s.*');
		$query->from('#__mams_artfeat as f');
		$query->join('LEFT', '#__mams_articles AS a ON a.art_id = f.af_art');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.feataccess IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$query->where('a.state >= 1');
		if (!in_array($cfg->ovgroup,$alvls)) { $query->where('a.art_publish_up <= NOW()'); $query->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }
		$query->order('f.ordering');
		$db->setQuery($query);
		$items = $db->loadObjectList();
								
		foreach ($items as &$i) {
			
			//Authors
			$i->auts = modMAMSFeatHelper::getFieldAuthors($i->art_id,"5",$alvls);
			
			//Categories
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

			// Tags
			$qc = $db->getQuery( true );
			$qc->select( 't.tag_id,t.tag_title,t.tag_alias' );
			$qc->from( '#__mams_arttag as at' );
			$qc->join( 'RIGHT', '#__mams_tags AS t ON at.at_tag = t.tag_id' );
			$qc->where( 'at.published >= 1' );
			$qc->where( 't.published >= 1' );
			$qc->where( 't.access IN (' . implode( ",", $alvls ) . ')' );
			$qc->where( 'at.at_art = ' . $i->art_id );
			$qc->order( 'at.ordering ASC' );
			$db->setQuery( $qc );
			$i->tags=$db->loadObjectList();
			
			if ($i->art_fielddata)
			{
				$registry = new JRegistry;
				$registry->loadString($i->art_fielddata);
				$i->art_fielddata = $registry->toObject();
			}
			if ($params->get('show_allfields',0)) {
				$i->fields = modMAMSFeatHelper::getArticleListFields($i->art_id,$alvls);
			}
		}
		
		return $items;
	}
	
	protected static function getArticleListFields($artid,$alvls) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('*,f.params as field_params,g.params as group_params');
		$query->from("#__mams_article_fields as f");
		$query->select('g.group_title');
		$query->join('LEFT', '#__mams_article_fieldgroups AS g ON g.group_id = f.field_group');
		$query->where('f.published >= 1');
		$query->where('f.access IN ('.implode(",",$alvls).')');
		$query->where('g.published >= 1');
		$query->where('g.access IN ('.implode(",",$alvls).')');
		$query->where('f.field_show_module = 1');
		$query->where('f.field_id >= 100');
		$query->order('f.ordering ASC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		foreach ($items as &$i) {
			switch ($i->field_type) {
				case "auths": $i->data = modMAMSFeatHelper::getFieldAuthors($artid,$i->field_id,$alvls); break;
				case "dloads": $i->data = modMAMSFeatHelper::getFieldDownloads($artid,$i->field_id,$alvls); break;
				case "links": $i->data = modMAMSFeatHelper::getFieldLinks($artid,$i->field_id,$alvls); break;
			}
			
			$registryf = new JRegistry;
			$registryf->loadString($i->field_params);
			$i->field_params = $registryf->toObject();
			
			$registryg = new JRegistry;
			$registryg->loadString($i->group_params);
			$i->group_params = $registryg->toObject();
		}
			
		return $items;
	}
	
	protected static function getFieldAuthors($artid, $fid, $alvls) {
		$db = JFactory::getDBO();
		$qa=$db->getQuery(true);
		$qa->select('a.auth_id,a.auth_fname,a.auth_mi,a.auth_lname,a.auth_titles,a.auth_alias,a.auth_sec');
		$qa->from('#__mams_artauth as aa');
		$qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
		$qa->where('aa.published >= 1');
		$qa->where('a.published >= 1');
		$qa->where('a.access IN ('.implode(",",$alvls).')');
		$qa->where('aa.aa_art = '.$artid);
		$qa->where('aa.aa_field = '.$fid);
		$qa->order('aa.ordering ASC');
		$db->setQuery($qa);
		return $db->loadObjectList();
	}
	
	protected static function getFieldDownloads($artid, $fid, $alvls) {
		$db = JFactory::getDBO();
		$qa=$db->getQuery(true);
		$qa->select('d.*');
		$qa->from('#__mams_artdl as ad');
		$qa->join('RIGHT','#__mams_dloads AS d ON ad.ad_dload = d.dl_id');
		$qa->where('ad.published >= 1');
		$qa->where('d.published >= 1');
		$qa->where('d.access IN ('.implode(",",$alvls).')');
		$qa->where('ad.ad_art = '.$artid);
		$qa->where('ad.ad_field = '.$fid);
		$qa->order('ad.ordering ASC');
		$db->setQuery($qa);
		return $db->loadObjectList();
	}
	
	protected static function getFieldLinks($artid, $fid, $alvls) {
		$db = JFactory::getDBO();
		$qa=$db->getQuery(true);
		$qa->select('l.*');
		$qa->from('#__mams_artlinks as al');
		$qa->join('RIGHT','#__mams_links AS l ON al.al_link = l.link_id');
		$qa->where('al.published >= 1');
		$qa->where('l.published >= 1');
		$qa->where('l.access IN ('.implode(",",$alvls).')');
		$qa->where('al.al_art = '.$artid);
		$qa->where('al.al_field = '.$fid);
		$qa->order('al.ordering ASC');
		$db->setQuery($qa);
		return $db->loadObjectList();
	}

}
