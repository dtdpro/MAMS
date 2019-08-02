<?php

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

require_once JPATH_SITE.'/components/com_mams/router.php';
require_once('components/com_mams/helpers/mams.php');

class plgSearchMAMS extends JPlugin
{
	function onContentSearchAreas()
	{
		static $areas = array(
			'mams' => 'Content'
			);
			return $areas;
	}

	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$cfg = MAMSHelper::getConfig();
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		$alvls = Array();
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
		$groups	= implode(',', $alvls);
		$tag = JFactory::getLanguage()->getTag();

		require_once JPATH_SITE.'/administrator/components/com_search/helpers/search.php';

		$searchText = $text;
		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}

		$limit			= $this->params->def('search_limit',		50);

		$nullDate		= $db->getNullDate();
		$date = JFactory::getDate();
		$now = $date->toSql();
		$rows = array();
			
		$text = trim($text);
		if ($text == '') {
			return array();
		}
		
		// search articles
		if ($limit > 0)
		{
			$wheres = array();
			switch ($phrase) {
				case 'exact':
					$text		= $db->quote('%'.$db->escape($text, true).'%', false);
					$wheres2	= array();
					$wheres2[]	= 'a.art_title LIKE '.$text;
					$wheres2[]	= 'a.art_content LIKE '.$text;
					$wheres2[]	= 'a.metakey LIKE '.$text;
					$wheres2[]	= 'a.metadesc LIKE '.$text;
					$wheres2[]	= 'a.art_desc LIKE '.$text;
					$where		= '(' . implode(') OR (', $wheres2) . ')';
					break;
	
				case 'all':
				case 'any':
				default:
					$words = explode(' ', $text);
					$wheres = array();
					foreach ($words as $word) {
						$word		= $db->quote('%'.$db->escape($word, true).'%', false);
						$wheres2	= array();
						$wheres2[]	= 'a.art_title LIKE '.$word;
						$wheres2[]	= 'a.art_content LIKE '.$word;
						$wheres2[]	= 'a.metakey LIKE '.$word;
						$wheres2[]	= 'a.metadesc LIKE '.$word;
						$wheres2[]	= 'a.art_desc LIKE '.$word;
						$wheres[]	= implode(' OR ', $wheres2);
					}
					$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
					break;
			}
	
			$morder = '';
			switch ($ordering) {
				case 'oldest':
					$order = 'a.art_publish_up ASC';
					break;
	
				case 'popular':
					$order = 'a.art_hits DESC';
					break;
	
				case 'alpha':
					$order = 'a.art_title ASC';
					break;
	
				case 'category':
					$order = 'c.sec_name ASC, a.art_title ASC';
					$morder = 'a.art_title ASC';
					break;
	
				case 'newest':
				default:
					$order = 'a.art_publish_up DESC';
					break;
			}
	
			$query	= $db->getQuery(true);


			$query->clear();
			$query->select('a.art_title AS title, a.metadesc as metadesc, a.metakey as metakey, a.art_publish_up AS created, a.art_id, a.params, '
						.'a.art_desc AS text, c.sec_name AS section, '
						.'CASE WHEN CHAR_LENGTH(a.art_alias) THEN CONCAT_WS(":", a.art_id, a.art_alias) ELSE a.art_id END as slug, '
						.'CASE WHEN CHAR_LENGTH(c.sec_alias) THEN CONCAT_WS(":", c.sec_id, c.sec_alias) ELSE c.sec_id END as catslug, '
						.'"2" AS browsernav');
			$query->from('#__mams_articles AS a');
			$query->innerJoin('#__mams_secs AS c ON c.sec_id=a.art_sec');
			$query->where('('. $where .')' . 'AND a.state >= 1 AND c.published >= 1 AND a.access IN ('.$groups.') '
						.'AND c.access IN ('.$groups.') AND a.art_publish_up <= NOW() AND (a.art_publish_down >= NOW() OR a.art_publish_down = "0000-00-00")');
			$query->group('a.art_id');
			$query->order($order);

			$db->setQuery($query, 0, $limit);
			$list = $db->loadObjectList();
			$limit -= count($list);

			if (isset($list))
			{
				foreach($list as $key => $item)
				{
					//Load up the Params
					$registry = new JRegistry;
					$registry->loadString($item->params);
					$item->params = $registry;

					//Merge Params
					$params = $app->getParams('com_mams');
					$item->params = $params->merge($item->params);

					//Categories
					$qc=$db->getQuery(true);
					$qc->select('c.cat_id,c.cat_title,c.cat_alias');
					$qc->from('#__mams_artcat as ac');
					$qc->join('RIGHT','#__mams_cats AS c ON ac.ac_cat = c.cat_id');
					$qc->where('ac.published >= 1');
					$qc->where('c.published >= 1');
					$qc->where('c.access IN ('.$groups.')');
					$qc->where('ac.ac_art = '.$item->art_id);
					$qc->order('ac.ordering ASC');
					$db->setQuery($qc);
					$cats=$db->loadObjectList();
					$cat = $cats[0];

					$link = "index.php?option=com_mams&view=article&artid=".$item->slug;
					if ($item->params->get('article_seclock',1)) $link .= "&secid=".$item->catslug;
					if ($item->params->get('article_catlock',1)) $link .= "&catid=".$cat->cat_id;

					$list[$key]->href = JRoute::_($link);
				}
			}
			$rows[] = $list;
		}
		
		// search authors
		if ($limit > 0)
		{
			$wheres = array();
			switch ($phrase) {
				case 'exact':
					$text		= $db->quote('%'.$db->escape($text, true).'%', false);
					$wheres2	= array();
					$wheres2[]	= 'a.auth_name LIKE '.$text;
					$wheres2[]	= 'a.auth_credentials LIKE '.$text;
					$wheres2[]	= 'a.auth_bio LIKE '.$text;
					$wheres2[]	= 'a.metakey LIKE '.$text;
					$wheres2[]	= 'a.metadesc LIKE '.$text;
					$where		= '(' . implode(') OR (', $wheres2) . ')';
					break;
		
				case 'all':
				case 'any':
				default:
					$words = explode(' ', $text);
					$wheres = array();
					foreach ($words as $word) {
						$word		= $db->quote('%'.$db->escape($word, true).'%', false);
						$wheres2	= array();
						$wheres2[]	= 'a.auth_name LIKE '.$word;
						$wheres2[]	= 'a.auth_credentials LIKE '.$word;
						$wheres2[]	= 'a.auth_bio LIKE '.$word;
						$wheres2[]	= 'a.metakey LIKE '.$word;
						$wheres2[]	= 'a.metadesc LIKE '.$word;
						$wheres[]	= implode(' OR ', $wheres2);
					}
					$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
					break;
			}
		
			$morder = '';
			switch ($ordering) {
				case 'oldest':
					$order = 'a.auth_added ASC';
					break;
		
				case 'alpha':
				case 'popular':
					$order = 'a.auth_lname ASC';
					break;
		
				case 'category':
					$order = 'c.sec_name ASC, a.auth_lname ASC';
					$morder = 'a.auth_lanme ASC';
					break;
		
				case 'newest':
				default:
					$order = 'a.auth_added DESC';
					break;
			}
		
			$query	= $db->getQuery(true);
		
		
			$query->clear();
			$query->select('a.auth_name AS title, a.metadesc as metadesc, a.metakey as metakey, a.auth_added AS created, '
					.'a.metadesc AS text, c.sec_name AS section, '
					.'CASE WHEN CHAR_LENGTH(a.auth_alias) THEN CONCAT_WS(":", a.auth_id, a.auth_alias) ELSE a.auth_id END as slug, '
					.'CASE WHEN CHAR_LENGTH(c.sec_alias) THEN CONCAT_WS(":", c.sec_id, c.sec_alias) ELSE c.sec_id END as catslug, '
					.'"2" AS browsernav');
			$query->from('#__mams_authors AS a');
			$query->innerJoin('#__mams_secs AS c ON c.sec_id=a.auth_sec');
			$query->where('('. $where .')' . 'AND a.published >= 1 AND c.published >= 1 AND a.access IN ('.$groups.') '
					.'AND c.access IN ('.$groups.') AND a.auth_added <= NOW() AND auth_mirror = 0 AND auth_exclude_search = 0');
			$query->group('a.auth_id');
			$query->order($order);
		
			$db->setQuery($query, 0, $limit);
			$list = $db->loadObjectList();
			$limit -= count($list);
		
			if (isset($list))
			{
				foreach($list as $key => $item)
				{
					$list[$key]->href = JRoute::_("index.php?option=com_mams&view=author&secid=".$item->catslug."&autid=".$item->slug);
				}
			}
			$rows[] = $list;
		}

		$results = array();
		if (count($rows))
		{
			foreach($rows as $row)
			{
				$new_row = array();
				foreach($row as $key => $article) {
					//if (searchHelper::checkNoHTML($article, $searchText, array('text', 'title', 'metadesc', 'metakey'))) {
						$new_row[] = $article;
					//}
				}
				$results = array_merge($results, (array) $new_row);
			}
		}

		return $results;
	}
}
