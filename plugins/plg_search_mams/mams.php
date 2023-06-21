<?php

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

require_once JPATH_SITE.'/components/com_mams/router.php';
require_once('components/com_mams/helpers/mams.php');

class plgSearchMAMS extends JPlugin
{
	var $articlesFoundIds = [];

	function onContentSearchAreas()
	{
		static $areas = array(
			'mams' => 'Content'
			);
			return $areas;
	}

	function onContentSearch($searchText, $phrase='', $ordering='', $areas=null)
	{
		$app = JFactory::getApplication();
		if ($app->isClient('site')) {
			$cfg = MAMSHelper::getConfig();
			$db = JFactory::getDbo();
			$user = JFactory::getUser();

			// Access levels
			$alvls = array();
			$alvls = $user->getAuthorisedViewLevels();
			$alvls = array_merge( $alvls, $cfg->reggroup );
			$groups = implode( ',', $alvls );

			// search module section limiter
			$module  = JModuleHelper::getModule( 'mod_searchsection' ); // Load up the params from the mod_searchsection module
			$moduleParams = new JRegistry();
			$moduleParams->loadString( $module->params );
			$selectedSection = $moduleParams->get( 'secid', '' ); // Assign selected value to variable
			$filterID = explode( ':', $selectedSection ); // Extract the ID number from the string
			$sectionIDFilter = $filterID[0];

			// load serach helper - no longer used
			//require_once JPATH_SITE . '/administrator/components/com_search/helpers/search.php';

			// return nothing if not searching MAMS content
			if ( is_array( $areas ) ) {
				if ( ! array_intersect( $areas, array_keys( $this->onContentSearchAreas() ) ) ) {
					return array();
				}
			}

			// get item limit
			$limit = $this->params->def( 'search_limit', 50 );

			// now
			$date     = JFactory::getDate();
			$now      = $date->toSql();

			// prep arrays
			$rows_authors  = [];
			$rows_authors_articles  = [];
			$rows_articles_title  = [];
			$rows_articles_teaser  = [];
			$rows_articles_body  = [];
			$authoredartcicleids = [];

			// trim the fat
			$searchText = trim( $searchText );

			// if nothing to search return nothing
			if ( $searchText == '' ) {
				return array();
			}

			// search authors
			if ( $limit > 0 && empty( $selectedSection ) ) {
				$wheres = array();
				switch ( $phrase ) {
					case 'exact':
						$stext     = $db->quote( '%' . $db->escape( $searchText, true ) . '%', false );
						$wheres2   = array();
						$wheres2[] = 'a.auth_name LIKE ' . $stext;
						$wheres2[] = 'a.auth_credentials LIKE ' . $stext;
						$wheres2[] = 'a.metakey LIKE ' . $stext;
						$wheres2[] = 'a.metadesc LIKE ' . $stext;
						$where     = '(' . implode( ') OR (', $wheres2 ) . ')';
						break;

					case 'all':
					case 'any':
					default:
						$words  = explode( ' ', $searchText );
						$wheres = array();
						foreach ( $words as $word ) {
							$word      = $db->quote( '%' . $db->escape( $word, true ) . '%', false );
							$wheres2   = array();
							$wheres2[] = 'LOWER(a.auth_name) LIKE LOWER(' . $word . ')';
							$wheres2[] = 'LOWER(a.auth_credentials) LIKE LOWER(' . $word . ')';
							$wheres2[] = 'LOWER(a.metakey) LIKE LOWER(' . $word . ')';
							$wheres2[] = 'LOWER(a.metadesc) LIKE LOWER(' . $word . ')';
							$wheres[]  = implode( ' OR ', $wheres2 );
						}
						$where = '(' . implode( ( $phrase == 'all' ? ') AND (' : ') OR (' ), $wheres ) . ')';
						break;
				}

				switch ( $ordering ) {
					case 'oldest':
						$order = 'a.auth_added ASC';
						break;

					case 'alpha':
					case 'popular':
						$order = 'a.auth_lname ASC';
						break;

					case 'category':
						$order  = 'c.sec_name ASC, a.auth_lname ASC';
						break;

					case 'newest':
					default:
						$order = 'a.auth_added DESC';
						break;
				}

				$query = $db->getQuery( true );


				$query->clear();
				$query->select( 'a.auth_name AS title, a.metadesc as metadesc, a.metakey as metakey, a.auth_added AS created, '
				                . 'a.metadesc AS text, c.sec_name AS section, a.auth_id, '
				                . 'CASE WHEN CHAR_LENGTH(a.auth_alias) THEN CONCAT_WS(":", a.auth_id, a.auth_alias) ELSE a.auth_id END as slug, '
				                . 'CASE WHEN CHAR_LENGTH(c.sec_alias) THEN CONCAT_WS(":", c.sec_id, c.sec_alias) ELSE c.sec_id END as catslug, '
				                . '"2" AS browsernav' );
				$query->from( '#__mams_authors AS a' );
				$query->innerJoin( '#__mams_secs AS c ON c.sec_id=a.auth_sec' );
				$query->where( '(' . $where . ')' . 'AND a.published >= 1 AND c.published >= 1 AND a.access IN (' . $groups . ') '
				               . 'AND c.access IN (' . $groups . ') AND a.auth_added <= NOW() AND auth_mirror = 0 AND auth_exclude_search = 0' );
				$query->group( 'a.auth_id' );
				$query->order( $order );

				$db->setQuery( $query, 0, $limit );
				$list      = $db->loadObjectList();
				$limit     -= count( $list );
				$authorids = array();
				if ( isset( $list ) ) {
					foreach ( $list as $key => $item ) {
						$list[ $key ]->href = JRoute::_( "index.php?option=com_mams&view=author&secid=" . $item->catslug . "&autid=" . $item->slug );
						$authorids[]        = $item->auth_id;
					}
				}
				$rows_authors[] = $list;

				// get articles form authors
				$aquery = $db->getQuery( true );
				$user   = JFactory::getUser();

				if ( count( $authorids ) ) {
					$aquery->select( 'aa.aa_art' );
					$aquery->from( '#__mams_artauth AS aa' );
					$aquery->where( 'aa.aa_auth IN ( ' . implode( ",", $authorids ) . ')' );
					$aquery->where( 'aa.aa_field = 5' );
					$aquery->where( 'aa.published >= 1' );
					$db->setQuery( $aquery );
					$authoredartcicleids = $db->loadColumn();
				}
			}

			// Article Ordering
			$articleOrder = '';
			switch ( $ordering ) {
				case 'oldest':
					$articleOrder = 'a.art_publish_up ASC';
					break;

				case 'popular':
					$articleOrder = 'a.art_hits DESC';
					break;

				case 'alpha':
					$articleOrder = 'a.art_title ASC';
					break;

				case 'category':
					$articleOrder  = 'c.sec_name ASC, a.art_title ASC';
					break;

				case 'newest':
				default:
				$articleOrder = 'a.art_publish_up DESC';
					break;
			}

			// search articles title
			if ( $limit > 0 ) {
				$wheres = array();
				switch ( $phrase ) {
					case 'exact':
						$stext     = $db->quote( '%' . $db->escape( $searchText, true ) . '%', false );
						$wheres2   = array();
						$wheres2[] = 'a.art_title LIKE ' . $stext;
						$where     = '(' . implode( ') OR (', $wheres2 ) . ')';
						break;

					case 'all':
					case 'any':
					default:
						$words  = explode( ' ', $searchText );
						$wheres = array();
						foreach ( $words as $word ) {
							$word      = $db->quote( '%' . $db->escape( $word, true ) . '%', false );
							$wheres2   = array();
							$wheres2[] = 'LOWER(a.art_title) LIKE LOWER(' . $word . ')';
							$wheres[]  = implode( ' OR ', $wheres2 );
						}
						$where = '(' . implode( ( $phrase == 'all' ? ') AND (' : ') OR (' ), $wheres ) . ')';
						break;
				}

				$list = $this->getArticles($articleOrder, $where, $selectedSection, $sectionIDFilter, $groups, $limit);

				if ( isset( $list ) ) {
					$limit -= count( $list );
					$rows_articles_title[] = $this->gatherLinks($list,$groups);
				}
			}

			// search articles teaser
			if ( $limit > 0 ) {
				$wheres = array();
				switch ( $phrase ) {
					case 'exact':
						$stext     = $db->quote( '%' . $db->escape( $searchText, true ) . '%', false );
						$wheres2   = array();
						$wheres2[] = 'a.metakey LIKE ' . $stext;
						$wheres2[] = 'a.metadesc LIKE ' . $stext;
						$wheres2[] = 'a.art_desc LIKE ' . $stext;
						$where     = '(' . implode( ') OR (', $wheres2 ) . ')';
						break;

					case 'all':
					case 'any':
					default:
						$words  = explode( ' ', $searchText );
						$wheres = array();
						foreach ( $words as $word ) {
							$word      = $db->quote( '%' . $db->escape( $word, true ) . '%', false );
							$wheres2   = array();
							$wheres2[] = 'LOWER(a.metakey) LIKE LOWER(' . $word . ')';
							$wheres2[] = 'LOWER(a.metadesc) LIKE LOWER(' . $word . ')';
							$wheres2[] = 'LOWER(a.art_desc) LIKE LOWER(' . $word . ')';
							$wheres[]  = implode( ' OR ', $wheres2 );
						}
						$where = '(' . implode( ( $phrase == 'all' ? ') AND (' : ') OR (' ), $wheres ) . ')';
						break;
				}

				$list = $this->getArticles($articleOrder, $where, $selectedSection, $sectionIDFilter, $groups, $limit);

				if ( isset( $list ) ) {
					$limit -= count( $list );
					$rows_articles_body[] = $this->gatherLinks($list,$groups);
				}
			}

			// search articles body
			if ( $limit > 0 ) {
				$wheres = array();
				switch ( $phrase ) {
					case 'exact':
						$stext     = $db->quote( '%' . $db->escape( $searchText, true ) . '%', false );
						$wheres2   = array();
						$wheres2[] = 'a.art_content LIKE ' . $stext;
						$where     = '(' . implode( ') OR (', $wheres2 ) . ')';
						break;

					case 'all':
					case 'any':
					default:
						$words  = explode( ' ', $searchText );
						$wheres = array();
						foreach ( $words as $word ) {
							$word      = $db->quote( '%' . $db->escape( $word, true ) . '%', false );
							$wheres2   = array();
							$wheres2[] = 'LOWER(a.art_content) LIKE LOWER(' . $word . ')';
							$wheres[]  = implode( ' OR ', $wheres2 );
						}
						$where = '(' . implode( ( $phrase == 'all' ? ') AND (' : ') OR (' ), $wheres ) . ')';
						break;
				}

				$list = $this->getArticles($articleOrder, $where, $selectedSection, $sectionIDFilter, $groups, $limit);

				if ( isset( $list ) ) {
					$limit -= count( $list );
					$rows_articles_body[] = $this->gatherLinks($list,$groups);
				}
			}

			// search authors articles
			if ( $limit > 0 && count( $authoredartcicleids ) && empty( $selectedSection )) {
				$where = 'a.art_id IN (' . implode( ",", $authoredartcicleids ) . ')'; // id by author

				$list = $this->getArticles($articleOrder, $where, $selectedSection, $sectionIDFilter, $groups, $limit);

				if ( isset( $list ) ) {
					$limit -= count( $list );
					$rows_authors_articles[] = $this->gatherLinks($list,$groups);
				}
			}

			$rows = array_merge($rows_articles_title, $rows_articles_teaser, $rows_authors, $rows_articles_body, $rows_authors_articles);

			$results = array();
			if ( count( $rows ) ) {
				foreach ( $rows as $row ) {
					$new_row = array();
					foreach ( $row as $key => $article ) {
						//if (searchHelper::checkNoHTML($article, $searchText, array('text', 'title', 'metadesc', 'metakey'))) {
						$new_row[] = $article;
						//}
					}
					$results = array_merge( $results, (array) $new_row );
				}
			}

			return $results;
		}

		return [];
	}

	private function getArticles($articleOrder, $where, $selectedSection, $sectionIDFilter, $groups, $limit) {
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );

		$query->clear();
		$query->select( 'a.art_title AS title, a.metadesc as metadesc, a.metakey as metakey, a.art_publish_up AS created, a.art_id, a.params, '
		                . 'a.art_desc AS text, c.sec_name AS section, a.access as art_access, c.access as sec_access, '
		                . 'CASE WHEN CHAR_LENGTH(a.art_alias) THEN CONCAT_WS(":", a.art_id, a.art_alias) ELSE a.art_id END as slug, '
		                . 'CASE WHEN CHAR_LENGTH(c.sec_alias) THEN CONCAT_WS(":", c.sec_id, c.sec_alias) ELSE c.sec_id END as catslug, '
		                . '"2" AS browsernav' );
		$query->from( '#__mams_articles AS a' );
		$query->innerJoin( '#__mams_secs AS c ON c.sec_id=a.art_sec' );
		$qwhere  = '(' . $where . ') ';
		$qwhere .= 'AND a.state >= 1 AND c.published >= 1 '; // published
		$qwhere .= 'AND a.art_publish_up <= NOW() AND (a.art_publish_down >= NOW() OR a.art_publish_down = "0000-00-00") '; // available
		$qwhere .= 'AND a.access IN (' . $groups . ') AND c.access IN (' . $groups . ') '; // can access
		if (count($this->articlesFoundIds)) $qwhere .= 'AND a.art_id NOT IN (' . implode( ",", $this->articlesFoundIds ) . ') '; // can access

		// search module section limiter
		if ( ! empty( $selectedSection ) ) {
			$qwhere .= ' AND (a.art_sec=(' . $sectionIDFilter . '))';
		}

		$query->where( $qwhere );
		$query->group( 'a.art_id' );
		$query->order( $articleOrder );

		$db->setQuery( $query, 0, $limit );
		$list = $db->loadObjectList();

		return $list;
	}

	private function gatherLinks($list,$groups) {
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		foreach ( $list as $key => $item ) {
			// add to found list
			$this->articlesFoundIds[] = $item->art_id;

			//Load up the Params
			$registry = new JRegistry;
			$registry->loadString( $item->params );
			$item->params = $registry;

			//Merge Params
			$params = $app->getParams( 'com_mams' );
			$item->params = $params->merge( $item->params );

			//Categories
			$qc = $db->getQuery( true );
			$qc->select( 'c.cat_id,c.cat_title,c.cat_alias' );
			$qc->from( '#__mams_artcat as ac' );
			$qc->join( 'RIGHT', '#__mams_cats AS c ON ac.ac_cat = c.cat_id' );
			$qc->where( 'ac.published >= 1' );
			$qc->where( 'c.published >= 1' );
			$qc->where( 'c.access IN (' . $groups . ')' );
			$qc->where( 'ac.ac_art = ' . $item->art_id );
			$qc->order( 'ac.ordering ASC' );
			$db->setQuery( $qc );
			$cats = $db->loadObjectList();
			$cat  = $cats[0];

			$link = "index.php?option=com_mams&view=article&artid=" . $item->slug;
			if ( $item->params->get( 'article_seclock', 1 ) ) {
				$link .= "&secid=" . $item->catslug;
			}
			if ( $item->params->get( 'article_catlock', 1 ) ) {
				$link .= "&catid=" . $cat->cat_id;
			}

			$list[ $key ]->href = JRoute::_( $link );
		}

		return $list;
	}
}
