<?php

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

require_once JPATH_SITE.'/components/com_mams/router.php';

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
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
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
		$now = $date->toMySQL();

		$text = trim($text);
		if ($text == '') {
			return array();
		}

		$wheres = array();
		switch ($phrase) {
			case 'exact':
				$text		= $db->Quote('%'.$db->getEscaped($text, true).'%', false);
				$wheres2	= array();
				$wheres2[]	= 'a.art_title LIKE '.$text;
				$wheres2[]	= 'a.art_content LIKE '.$text;
				$wheres2[]	= 'a.art_keywords LIKE '.$text;
				$wheres2[]	= 'a.art_desc LIKE '.$text;
				$where		= '(' . implode(') OR (', $wheres2) . ')';
				break;

			case 'all':
			case 'any':
			default:
				$words = explode(' ', $text);
				$wheres = array();
				foreach ($words as $word) {
					$word		= $db->Quote('%'.$db->getEscaped($word, true).'%', false);
					$wheres2	= array();
					$wheres2[]	= 'a.art_title LIKE '.$word;
					$wheres2[]	= 'a.art_content LIKE '.$word;
					$wheres2[]	= 'a.art_keywords LIKE '.$word;
					$wheres2[]	= 'a.art_desc LIKE '.$word;
					$wheres[]	= implode(' OR ', $wheres2);
				}
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				break;
		}

		$morder = '';
		switch ($ordering) {
			case 'oldest':
				$order = 'a.art_published ASC';
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
				$order = 'a.art_published DESC';
				break;
		}

		$rows = array();
		$query	= $db->getQuery(true);

		// search articles
		if ($limit > 0)
		{
			$query->clear();
			$query->select('a.art_title AS title, a.art_desc as metadesc, a.art_keywords as metakey, a.art_published AS created, '
						.'a.art_desc AS text, c.sec_name AS section, '
						.'CASE WHEN CHAR_LENGTH(a.art_alias) THEN CONCAT_WS(":", a.art_id, a.art_alias) ELSE a.art_id END as slug, '
						.'CASE WHEN CHAR_LENGTH(c.sec_alias) THEN CONCAT_WS(":", c.sec_id, c.sec_alias) ELSE c.sec_id END as catslug, '
						.'"2" AS browsernav');
			$query->from('#__mams_articles AS a');
			$query->innerJoin('#__mams_secs AS c ON c.sec_id=a.art_sec');
			$query->where('('. $where .')' . 'AND a.published >= 1 AND c.published >= 1 AND a.access IN ('.$groups.') '
						.'AND c.access IN ('.$groups.') AND a.art_published <= NOW()');
			$query->group('a.art_id');
			$query->order($order);

			$db->setQuery($query, 0, $limit);
			$list = $db->loadObjectList();
			$limit -= count($list);

			if (isset($list))
			{
				foreach($list as $key => $item)
				{
					$list[$key]->href = JRoute::_("index.php?option=com_mams&view=article&secid=".$item->catslug."&artid=".$item->slug);
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
					if (searchHelper::checkNoHTML($article, $searchText, array('text', 'title', 'metadesc', 'metakey'))) {
						$new_row[] = $article;
					}
				}
				$results = array_merge($results, (array) $new_row);
			}
		}

		return $results;
	}
}
