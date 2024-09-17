<?php

use YooTheme\Database;

class MAMSProvider
{
    public static function getCats($artcount = false, $parent=0,$orderby="titasc",$onlyFeatCat=false,$restrictFeatCat=false) {
		$db = JFactory::getDBO();
        $sewn = JFactory::getSession();
        $sessionid = $sewn->getId();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$query->select('c.*');
		$query->from('#__mams_cats AS c');
		if (is_array($parent)) {
			$query->where( 'c.parent_id IN (' . implode(",",$parent).')' );
		} else {
			$query->where( 'c.parent_id = ' . (int) $parent );
		}
		$query->where('c.published >= 1');
		$query->where('c.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		switch ($orderby) {
			case "titasc": $query->order('cat_title ASC'); break;
			case "titdsc": $query->order('cat_title DESC'); break;
            case "orderasc": $query->order('ordering ASC'); break;
            case "orderdsc": $query->order('ordering ASC'); break;
			default: $query->order('cat_title ASC'); break;
		}
		if ($onlyFeatCat) {
			$query->where('c.cat_featured = 1');
		}
		$db->setQuery($query);
		$items = $db->loadObjectList();


        $q = $db->getQuery(true);
        $q->insert('#__mams_track');
        $q->columns(array($db->quoteName('mt_item'),$db->quoteName('mt_type'),$db->quoteName('mt_user'),$db->quoteName('mt_session'),$db->quoteName('mt_ipaddr')));
        $q->values('"0","listcats","'.$user->id.'","'.$sessionid.'","'.$_SERVER['REMOTE_ADDR'].'"');
        $db->setQuery($q);
        $db->execute();

		// Remove categories not in Access Level List when enabled
		if ($onlyFeatCat || $restrictFeatCat) {
			foreach ($items as $k => $i) {
				$alintersect = array_intersect(explode(",",$i->cat_feataccess),$user->getAuthorisedViewLevels());
				if (!count($alintersect)) {
					unset($items[$k]);
				}
			}
		}
		
		if ($artcount) {
			foreach ($items as &$i) {
				$query = $db->getQuery(true);
				$query->select('ac.ac_art');
				$query->from('#__mams_artcat AS ac');
				$query->where('ac.ac_cat = '.$i->cat_id);
				$query->where('ac.published >= 1');
				$db->setQuery($query);
				$arts = $db->loadColumn();
				$i->cat_title = $i->cat_title.' ('.count($arts).')';
			}
		}
		
		return $items;
	}

	public static function getArticles($sec=null,$category=null,$tag=null,$limit=5,$orderby1="a.art_publish_up DESC",$orderby2="s.lft ASC",$orderby3="a.ordering ASC",$restrictFeat=false,$additionalFields=false) {
		// getheper for config
		require_once('components/com_mams/helpers/mams.php');

		$db		= JFactory::getDbo();
        $sewn = JFactory::getSession();
        $sessionid = $sewn->getId();
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();

		$alvls = Array();
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);

        if ($sec) {
            $secs = array_map('trim', explode(",", $sec));
            foreach ($secs as $s) {
                $q = $db->getQuery(true);
                $q->insert('#__mams_track');
                $q->columns(array($db->quoteName('mt_item'),$db->quoteName('mt_type'),$db->quoteName('mt_user'),$db->quoteName('mt_session'),$db->quoteName('mt_ipaddr')));
                $q->values('"'.$db->escape($s).'","seclist","'.$user->id.'","'.$sessionid.'","'.$_SERVER['REMOTE_ADDR'].'"');
                $db->setQuery($q);
                $db->execute();
            }
        } else {
            $secs = [];
        }

        if ($category) {
            $cats = array_map('trim', explode(",", $category));
            foreach ($cats as $c) {
                $q = $db->getQuery(true);
                $q->insert('#__mams_track');
                $q->columns(array($db->quoteName('mt_item'),$db->quoteName('mt_type'),$db->quoteName('mt_user'),$db->quoteName('mt_session'),$db->quoteName('mt_ipaddr')));
                $q->values('"'.$db->escape($c).'","catlist","'.$user->id.'","'.$sessionid.'","'.$_SERVER['REMOTE_ADDR'].'"');
                $db->setQuery($q);
                $db->execute();
            }
        } else {
            $cats = [];
        }

        if ($tag) {
            $tags = array_map('trim', explode(",", $tag));
            foreach ($tags as $t) {
                $q = $db->getQuery(true);
                $q->insert('#__mams_track');
                $q->columns(array($db->quoteName('mt_item'),$db->quoteName('mt_type'),$db->quoteName('mt_user'),$db->quoteName('mt_session'),$db->quoteName('mt_ipaddr')));
                $q->values('"'.$db->escape($t).'","taglist","'.$user->id.'","'.$sessionid.'","'.$_SERVER['REMOTE_ADDR'].'"');
                $db->setQuery($q);
                $db->execute();
            }
        } else {
            $tags = [];
        }
		// Filter categories
		$catartids = [];
		if (count($cats)) {
			$qcat = $db->getQuery(true);
			$qcat->select('ac.ac_art');
			$qcat->from('#__mams_artcat AS ac');
			$qcat->where('ac.ac_cat IN ('.implode(',',$cats).')');
			$qcat->where('ac.published >= 1');
			$db->setQuery($qcat);
			$catartids = $db->loadColumn(0);
		}

		// Filter tags
		$tagartids = [];
		if (count($tags)) {
			$tcat = $db->getQuery(true);
			$tcat->select('at.at_art');
			$tcat->from('#__mams_arttag AS at');
			$tcat->where('at.at_tag IN ('.implode(',',$tags).')');
			$tcat->where('at.published >= 1');
			$db->setQuery($tcat);
			$tagartids = $db->loadColumn(0);
		}

		if (count($tagartids) && count($catartids)) {
			$artids = array_intersect($catartids,$tagartids);
		} else if (count($tagartids)) {
			$artids = $tagartids;
		} else if (count($catartids)) {
			$artids = $catartids;
		} else {
			$artids = [];
		}

		$query	= $db->getQuery(true);

		$query->select('a.*,s.*,a.art_title as testing');
		$query->from('#__mams_articles as a');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.access IN ('.implode(",",$alvls).')');
		if ($restrictFeat) $query->where('a.feataccess IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$query->where('a.state >= 1');
		if (count($secs)) $query->where('a.art_sec IN ('.implode(',',$secs).')');
		if (!in_array($cfg->ovgroup,$alvls)) { $query->where('a.art_publish_up <= NOW()'); $query->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }
		if (count($artids) > 0) $query->where('a.art_id IN ('.implode(",",$artids).')');
		$query->order($orderby1.', '.$orderby2.', '.$orderby3);
		$db->setQuery($query,0,$limit);
		$items = $db->loadObjectList();

		foreach ($items as &$i) {

			//Authors
			$qa=$db->getQuery(true);
			$qa->select('a.auth_id,a.auth_name,a.auth_alias,a.auth_sec');
			$qa->from('#__mams_artauth as aa');
			$qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
			$qa->where('aa.published >= 1');
			$qa->where('a.published >= 1');
			$qa->where('a.access IN ('.implode(",",$alvls).')');
			$qa->where('aa.aa_art = '.$i->art_id);
			$qa->where('aa.aa_field = 5');
			$qa->order('aa.ordering ASC');
			$db->setQuery($qa);
			$i->auts = $db->loadObjectList();

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

            if ($additionalFields) {
                if ($i->art_fielddata) {
                    $registry = new JRegistry;
                    $registry->loadString($i->art_fielddata);
                    $i->art_fielddata = $registry->toObject();
                }

                $fields = MAMSProvider::getArticleFields();

                foreach ($fields as &$f) {
                    switch ($f->field_type) {
                        case "auths":
                            $f->data = MAMSProvider::getFieldAuthors($i->art_id, $f->field_id);
                            break;
                        case "dloads":
                            $f->data = MAMSProvider::getFieldDownloads($i->art_id, $f->field_id);
                            break;
                        case "links":
                            $f->data = MAMSProvider::getFieldLinks($i->art_id, $f->field_id);
                            break;
                    }
                }

                $i->fields = $fields;
            }

		}

		return $items;
	}

    public static function getArticleFields() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('f.*');
        $query->from("#__mams_article_fields as f");
        $query->select('g.group_title,g.group_show_title,g.group_name');
        $query->join('LEFT', '#__mams_article_fieldgroups AS g ON g.group_id = f.field_group');
        $query->where('f.published >= 1');
        $query->where('g.published >= 1');
        $query->where("f.field_type in ('textfield','textbox','editor','auths','dloads','links')");//'media','images','artauthedlink'
        $query->order('f.ordering ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();

        return $items;
    }

    public static function getFeaturedArticles($limit=5,$restrictFeat=false)
    {
        // getheper for config
        require_once('components/com_mams/helpers/mams.php');

        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $cfg = MAMSHelper::getConfig();

        $alvls = Array();
        $alvls = $user->getAuthorisedViewLevels();
        $alvls = array_merge($alvls,$cfg->reggroup);

        $query = $db->getQuery(true);

        $query->select('f.*,a.*,s.*');
        $query->from('#__mams_artfeat as f');
        $query->join('LEFT', '#__mams_articles AS a ON a.art_id = f.af_art');
        $query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
        if ($restrictFeat) $query->where('a.feataccess IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
        $query->where('a.state >= 1');
        if (!in_array($cfg->ovgroup,$alvls)) { $query->where('a.art_publish_up <= NOW()'); $query->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }
        $query->order('f.ordering');
        $db->setQuery($query,0,$limit);
        $items = $db->loadObjectList();

        foreach ($items as &$i) {

            //Authors
            $qa=$db->getQuery(true);
            $qa->select('a.auth_id,a.auth_name,a.auth_alias,a.auth_sec');
            $qa->from('#__mams_artauth as aa');
            $qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
            $qa->where('aa.published >= 1');
            $qa->where('a.published >= 1');
            $qa->where('a.access IN ('.implode(",",$alvls).')');
            $qa->where('aa.aa_art = '.$i->art_id);
            $qa->where('aa.aa_field = 5');
            $qa->order('aa.ordering ASC');
            $db->setQuery($qa);
            $i->auts = $db->loadObjectList();

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

            /*if ($i->art_fielddata)
            {
                $registry = new JRegistry;
                $registry->loadString($i->art_fielddata);
                $i->art_fielddata = $registry->toObject();
            }
            if ($params->get('show_allfields',0)) {
                $i->fields = modMAMSFeatHelper::getArticleListFields($i->art_id,$alvls);
            }*/
        }

        return $items;
    }

	public static function secList() {
		$db	= JFactory::getDbo();
		$qc=$db->getQuery(true);
		$qc->select('sec_id,sec_name,sec_alias');
		$qc->from('#__mams_secs');
		$qc->where('published >= 1');
		$qc->order('sec_name ASC');
		$db->setQuery($qc);
		return $db->loadObjectList();
	}

	public static function catList() {
		$db	= JFactory::getDbo();
		$qc=$db->getQuery(true);
		$qc->select('cat_id,cat_title,cat_alias');
		$qc->from('#__mams_cats');
		$qc->where('published >= 1');
		$qc->order('cat_title ASC');
		$db->setQuery($qc);
		return $db->loadObjectList();
	}

	public static function tagList() {
		$db	= JFactory::getDbo();
		$qc=$db->getQuery(true);
		$qc->select('tag_id,tag_title,tag_alias');
		$qc->from('#__mams_tags');
		$qc->where('published >= 1');
		$qc->order('tag_title ASC');
		$db->setQuery($qc);
		return $db->loadObjectList();
	}

    public static function getFieldAuthors($artid, $fid) {
        $db = JFactory::getDBO();
        $qa=$db->getQuery(true);
        $qa->select('a.auth_id,a.auth_fname,a.auth_mi,a.auth_lname,a.auth_titles,a.auth_alias,a.auth_sec,a.auth_name,a.auth_image,a.auth_credentials');
        $qa->from('#__mams_artauth as aa');
        $qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
        $qa->where('aa.published >= 1');
        $qa->where('a.published >= 1');
        $qa->where('aa.aa_art = '.$artid);
        $qa->where('aa.aa_field = '.$fid);
        $qa->order('aa.ordering ASC');
        $db->setQuery($qa);
        return $db->loadObjectList();
    }

    public static function getFieldDownloads($artid, $fid) {
        $db = JFactory::getDBO();
        $qa=$db->getQuery(true);
        $qa->select('d.*');
        $qa->from('#__mams_artdl as ad');
        $qa->join('RIGHT','#__mams_dloads AS d ON ad.ad_dload = d.dl_id');
        $qa->where('ad.published >= 1');
        $qa->where('d.published >= 1');
        $qa->where('ad.ad_art = '.$artid);
        $qa->where('ad.ad_field = '.$fid);
        $qa->order('ad.ordering ASC');
        $db->setQuery($qa);
        return $db->loadObjectList();
    }

    public static function getFieldLinks($artid, $fid) {
        $db = JFactory::getDBO();
        $qa=$db->getQuery(true);
        $qa->select('l.*');
        $qa->from('#__mams_artlinks as al');
        $qa->join('RIGHT','#__mams_links AS l ON al.al_link = l.link_id');
        $qa->where('al.published >= 1');
        $qa->where('l.published >= 1');
        $qa->where('al.al_art = '.$artid);
        $qa->where('al.al_field = '.$fid);
        $qa->order('al.ordering ASC');
        $db->setQuery($qa);
        return $db->loadObjectList();
    }
}
