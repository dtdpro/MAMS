<?php


// no direct access
defined('_JEXEC') or die;

class modMAMSMostHelper
{
	static function getArticles($params)
	{
		$db		= JFactory::getDbo();
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();

		$alvls = Array();
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);

		if (!$params->get('show_featured',1)) $featured = modMAMSMostHelper::getFeatured();

		if (count($params->get('exclude_cats',array()))) {
			$excluded_cats = array();
			foreach ($params->get('exclude_cats') as $ec) {
				$excluded_cats[] = (int)$ec;
			}
			$qea=$db->getQuery(true);
			$qea->select('ac_art');
			$qea->from('#__mams_artcat');
			$qea->where('ac_cat IN ('.implode(",",$excluded_cats).')');
			$db->setQuery($qea);
			$exclude_articles = $db->loadColumn();
		} else {
			$exclude_articles = array();
		}

		$query	= $db->getQuery(true);

		$query->select('t.mt_item,count(*) as arthits');
		$query->from('#__mams_track AS t');
		$query->where('t.mt_type = "article"');
		$query->where('t.mt_time >= DATE_SUB(NOW(), INTERVAL '.$params->get('num_days_read',30).' day)');
		$query->group('t.mt_item');
		$query->order('arthits DESC');
		$db->setQuery($query);
		$artids = $db->loadColumn();

		if ($artids) {
			$query = $db->getQuery( true );

			$query->select( 'a.*,s.*' );
			$query->from( '#__mams_articles AS a' );
			$query->join( 'RIGHT', '#__mams_secs AS s ON s.sec_id = a.art_sec' );
			if ( ! $params->get( 'show_excluded', 0 ) ) {
				$query->where( 'a.art_excluded = 0' );
			}
			$query->where( 'a.feataccess IN (' . implode( ",", $user->getAuthorisedViewLevels() ) . ')' );
			$query->where( 'a.state >= 1' );
			if ( ! in_array( $cfg->ovgroup, $alvls ) ) {
				$query->where( 'a.art_publish_up <= NOW()' );
				$query->where( '(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")' );
			}
			$query->where( 'a.art_publish_up >= DATE_SUB(NOW(), INTERVAL ' . $params->get( 'num_days_old',
					90 ) . ' day)' );
			$query->where( 'a.art_id IN(' . implode( ",", $artids ) . ')' );
			if ( ! $params->get( 'show_featured', 1 ) && count( $featured ) ) {
				$query->where( "a.art_id NOT IN (" . implode( ',', $featured ) . ')' );
			}
			if ( count( $exclude_articles ) ) {
				$query->where( "a.art_id NOT IN (" . implode( ',', $exclude_articles ) . ')' );
			}
			$query->order( 'FIELD(art_id,' . implode( ",", $artids ) . ')' );
			$db->setQuery( $query, 0, $params->get( 'count', 5 ) );
			$items = $db->loadObjectList();

			foreach ( $items as &$i ) {

				//Authors
				$i->auts = modMAMSMostHelper::getFieldAuthors( $i->art_id, "5", $alvls );

				//Categories
				$qc = $db->getQuery( true );
				$qc->select( 'c.cat_id,c.cat_title,c.cat_alias' );
				$qc->from( '#__mams_artcat as ac' );
				$qc->join( 'RIGHT', '#__mams_cats AS c ON ac.ac_cat = c.cat_id' );
				$qc->where( 'ac.published >= 1' );
				$qc->where( 'c.published >= 1' );
				$qc->where( 'c.access IN (' . implode( ",", $alvls ) . ')' );
				$qc->where( 'ac.ac_art = ' . $i->art_id );
				$qc->order( 'ac.ordering ASC' );
				$db->setQuery( $qc );
				$i->cats = $db->loadObjectList();

				if ( $i->art_fielddata ) {
					$registry = new JRegistry;
					$registry->loadString( $i->art_fielddata );
					$i->art_fielddata = $registry->toObject();
				}
				if ( $params->get( 'show_allfields', 0 ) ) {
					$i->fields = modMAMSMostHelper::getArticleListFields( $i->art_id, $alvls );
				}
			}
		} else {
			$items=array();
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
				case "auths": $i->data = modMAMSMostHelper::getFieldAuthors($artid,$i->field_id,$alvls); break;
				case "dloads": $i->data = modMAMSMostHelper::getFieldDownloads($artid,$i->field_id,$alvls); break;
				case "links": $i->data = modMAMSMostHelper::getFieldLinks($artid,$i->field_id,$alvls); break;
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

	protected static function getFeatured() {
		$db = JFactory::getDBO();
		$query=$db->getQuery(true);
		$query->select('af_art');
		$query->from('#__mams_artfeat as f');
		$db->setQuery($query);
		return $db->loadColumn();
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
