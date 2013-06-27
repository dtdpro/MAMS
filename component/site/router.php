<?php

defined('_JEXEC') or die;

jimport('joomla.application.categories');





function MAMSBuildRoute(&$query)
{
	$items = Array();
	$default = 0;
	$foundart = 0;
	$foundsec = 0;
	$foundcat = 0;
	$foundcatsec = 0;
	$foundaut = 0;
	$segments = array();
	$app = JFactory::getApplication();
	$menu	= $app->getMenu();
	$items	= $menu->getItems('component', 'com_mams');
	
	if (isset($query['view'])) {
		$view = $query['view'];
	}
	
	foreach ($items as $mi) {
		if (!empty($mi->query['artid']) && ((int)$mi->query['artid'] == (int)$query['artid'])) {
			$foundart = $mi->id;
		}
		if (!empty($mi->query['secid']) && ((int)$mi->query['secid'] == (int)$query['secid'])) {
			$foundsec = $mi->id;
		}
		if (!empty($mi->query['catid']) && ((int)$mi->query['catid'] == (int)$query['catid'])) {
			$foundcat = $mi->id;
		}
		if (!empty($mi->query['catid']) && ((int)$mi->query['catid'] == (int)$query['catid']) && !empty($mi->query['secid']) && ((int)$mi->query['secid'] == (int)$query['secid'])) {
			$foundcatsec = $mi->id;
		}
		if (!empty($mi->query['autid']) && ((int)$mi->query['autid'] == (int)$query['autid'])) {
			$foundaut = $mi->id;
		}
	}
	
	$default = $query['Itemid'];
	
	if ($view == 'article') {
		if ($foundart) {
			$query['Itemid'] = $foundart;
			unset ($query['artid']);
			unset ($query['view']);
			unset ($query['secid']);
		} else if ($foundsec) {
			$query['Itemid'] = $foundsec;
			unset ($query['view']);
			unset ($query['secid']);
			if (strpos($query['artid'], ':') === false) {
				$db = JFactory::getDbo();
				$aquery = $db->setQuery($db->getQuery(true)
					->select('art_alias')
					->from('#__mams_articles')
					->where('art_id='.(int)$query['artid'])
				);
				$alias = $db->loadResult();
				$query['artid'] = $query['artid'].':'.$alias;
			}
			$segments[] = $query['artid'];
			unset ($query['artid']);
			
		} else {
			$query['Itemid'] = $default;
		}
	}
	
	if ($view == 'artlist') {
		if ($foundaut) {
			$query['Itemid'] = $foundaut;
			unset ($query['view']);
			unset ($query['autid']);
			unset ($query['layout']);
		} elseif ($foundcatsec) {
			$query['Itemid'] = $foundcatsec;
			unset ($query['view']);
			unset ($query['catid']);
			unset ($query['secid']);
			unset ($query['layout']);
		} elseif ($foundcat) {
			$query['Itemid'] = $foundcat;
			unset ($query['view']);
			unset ($query['catid']);
			unset ($query['secid']);
			unset ($query['layout']);
		} elseif ($foundsec) {
			$query['Itemid'] = $foundsec;
			unset ($query['view']);
			unset ($query['layout']);
			if (isset($query['catid'])) {
				if (strpos($query['secid'], ':') === false) {
					$db = JFactory::getDbo();
					$aquery = $db->setQuery($db->getQuery(true)
							->select('sec_alias')
							->from('#__mams_secs')
							->where('sec_id='.(int)$query['secid'])
					);
					$alias = $db->loadResult();
					$query['secid'] = $query['secid'].':'.$alias;
				}
				$segments[] = $query['secid'];
				unset ($query['secid']);
				if (strpos($query['catid'], ':') === false) {
					$db = JFactory::getDbo();
					$aquery = $db->setQuery($db->getQuery(true)
							->select('cat_alias')
							->from('#__mams_cats')
							->where('cat_id='.(int)$query['catid'])
					);
					$alias = $db->loadResult();
					$query['catid'] = $query['catid'].':'.$alias;
				}
				$segments[] = $query['catid'];
				unset ($query['catid']);
			} else {
				unset ($query['secid']);
			}
		} else {
			$query['Itemid'] = $default;
		}
	}
	
	if ($view == 'author') {
		if ($foundaut) {
			$query['Itemid'] = $foundaut;
			unset ($query['view']);
			unset ($query['autid']);
			unset ($query['secid']);
		} else if ($foundsec) {
			$query['Itemid'] = $foundsec;
			unset ($query['view']);
			unset ($query['secid']);
			unset ($query['layout']);
			if (strpos($query['autid'], ':') === false && isset($query['autid'])) {
				$db = JFactory::getDbo();
				$aquery = $db->setQuery($db->getQuery(true)
					->select('auth_alias')
					->from('#__mams_authors')
					->where('auth_id='.(int)$query['autid'])
				);
				$alias = $db->loadResult();
				$query['autid'] = $query['autid'].':'.$alias;
			}
			$segments[] = $query['autid'];
			unset ($query['autid']);
		} else {
			$query['Itemid'] = $default;
		}
	}
	
	
	
	
	return $segments;
	
}



/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 * @since	1.5
 */
function MAMSParseRoute($segments)
{
	$vars = array();
	
	//Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$params = JComponentHelper::getParams('com_mams');
	$advanced = $params->get('sef_advanced_link', 0);
	$db = JFactory::getDBO();

	// Count route segments
	$count = count($segments);

	// Standard routing for articles.  If we don't pick up an Itemid then we get the view from the segments
	// the first segment is the view and the last segment is the id of the article or category.
	if (!isset($item)) {
		$vars['view']	= $segments[0];
		$vars['artid']		= $segments[$count - 1];

		return $vars;
	}

	// if there is only one segment, then it points to either an article, section, or author
	// we test it first to see if it is a section.  If the id and alias match a section
	// then we assume it is a section.  If they don't we check for article, etc...
	if ($count == 1) {
		// we check to see if an alias is given.  If not, we assume it is an article
		if (strpos($segments[0], ':') === false) {
			$vars['view'] = 'article';
			$vars['artid'] = (int)$segments[0];
			return $vars;
		}

		list($id, $alias) = explode(':', $segments[0], 2);

		// first we check if it is a section
		$query = 'SELECT sec_alias, sec_id FROM #__mams_secs WHERE sec_id = '.(int)$id;
		$db->setQuery($query);
		$section = $db->loadObject();

		if ($section && $section->sec_alias == $alias) {
			$vars['view'] = 'artlist';
			$vars['layout'] = 'section';
			$vars['secid'] = $id;

			return $vars;
		} else {
			// check for aricle
			$query = 'SELECT art_alias, art_id, art_sec FROM #__mams_articles WHERE art_id = '.(int)$id;
			$db->setQuery($query);
			$article = $db->loadObject();

			if ($article && $article->art_alias == $alias) {
				$vars['view'] = 'article';
				$vars['secid'] = (int)$article->art_sec;
				$vars['artid'] = (int)$id;

				return $vars;
			} else {
				// check for author
				$query = 'SELECT auth_alias, auth_id FROM #__mams_authors WHERE auth_id = '.(int)$id;
				$db->setQuery($query);
				$author = $db->loadObject();
				
				if ($author && $author->auth_alias == $alias) {
					$vars['view'] = 'author';
					$vars['autid'] = (int)$id;
					return $vars;
				} else {
					// check for category
					$query = 'SELECT cat_alias, cat_id FROM #__mams_cats WHERE cat_id = '.(int)$id;
					$db->setQuery($query);
					$cat = $db->loadObject();
					
					if ($cat && $cat->cat_alias == $alias) {
						$vars['view'] = 'artlist';
						$vars['layout'] = 'category';
						$vars['catid'] = (int)$id;
						return $vars;
					}
				}
				
			}
		}
	}

	// if there was more than one segment, then we can determine where the URL points to
	// because the first segment will have the target section id prepended to it.  If the
	// last segment has a number prepended we then check for article, cat list or auth list.
	if (!$advanced) {
		$sec_id = (int)$segments[0];

		list($id, $alias) = explode(':', $segments[$count-1], 2);

		if ($id > 0) {
			// first we check if it is a cat
			$query = 'SELECT cat_alias, cat_alias FROM #__mams_cats WHERE cat_id = '.(int)$id;
			$db->setQuery($query);
			$category = $db->loadObject();
	
			if ($category && $category->cat_alias == $alias) {
				$vars['view'] = 'artlist';
				$vars['layout'] = 'category';
				$vars['catid'] = $id;
				$vars['secid'] = $sec_id;
	
				return $vars;
			} else {
				// check for aricle
				$query = 'SELECT art_alias, art_id, art_sec FROM #__mams_articles WHERE art_id = '.(int)$id;
				$db->setQuery($query);
				$article = $db->loadObject();
	
				if ($article && $article->art_alias == $alias) {
					$vars['view'] = 'article';
					$vars['secid'] = (int)$article->art_sec;
					$vars['artid'] = (int)$id;
	
					return $vars;
				} else {
					// check for author
					$query = 'SELECT auth_alias, auth_id FROM #__mams_authors WHERE auth_id = '.(int)$id;
					$db->setQuery($query);
					$author = $db->loadObject();
					
					if ($author && $author->auth_alias == $alias) {
						$vars['view'] = 'artlist';
						$vars['layout'] = 'author';
						$vars['autid'] = (int)$id;
						return $vars;
					}
					
				}
			}
			$vars['secid'] = $sec_id;
		} else {
			$vars['view'] = 'artlist';
			$vars['secid'] = $sec_id;
		}

		return $vars;
	}

	// we get the section id from the menu item and search from there
	$secid = $item->query['secid'];
	$artid = $item->query['artid'];
	
	// first we check if it is a section
	$query = 'SELECT sec_alias, sec_id FROM #__mams_secs WHERE sec_id = '.(int)$id;
	$db->setQuery($query);
	$section = $db->loadObject();

	if (!$section) {
		JError::raiseError(404, JText::_('COM_MAMS_ERROR_SECTION_NOT_FOUND'));
		return $vars;
	}

	$vars['secid'] = $secid;
	$vars['artid'] = $artid;
	$found = 0;

	foreach($segments as $segment)
	{
		$segment = str_replace(':', '-',$segment);

		if ($section->sec_alias == $segment) {
			$vars['secid'] = $category->id;
			$vars['view'] = 'artlist';
			$vars['layout'] = 'section';
			$found = 1;
		}
		
		if ($found == 0) {
			if ($advanced) {
				$db = JFactory::getDBO();
				$query = 'SELECT art_id FROM #__mams_articles WHERE art_sec = '.$vars['secid'].' AND art_alias = '.$db->Quote($segment);
				$db->setQuery($query);
				$cid = $db->loadResult();
			} else {
				$cid = $segment;
			}

			$vars['artid'] = $cid;

			$vars['view'] = 'article';
		}

		$found = 0;
	}

	return $vars;
}
