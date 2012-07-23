<?php

defined('_JEXEC') or die;

jimport('joomla.application.categories');





function MAMSBuildRoute(&$query)
{
	$segments	= array();

	// get a menu item based on Itemid or currently active
	$app		= JFactory::getApplication();
	$menu		= $app->getMenu();
	$params		= JComponentHelper::getParams('com_mams');
	$advanced	= $params->get('sef_advanced_link', 0);

	// we need a menu item.  Either the one specified in the query, or the current active one if none specified
	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
		$menuItemGiven = false;
	}
	else {
		$menuItem = $menu->getItem($query['Itemid']);
		$menuItemGiven = true;
	}

	if (isset($query['view'])) {
		$view = $query['view'];
	}
	else {
		// we need to have a view in the query or it is an invalid URL
		return $segments;
	}
	
	if (isset($query['layout'])) {
		$layout = $query['layout'];
	} else {
		$layout = 'default';
	}

	// are we dealing with an article or category that is attached to a menu item?
	if (($menuItem instanceof stdClass) && $menuItem->query['view'] == $query['view'] && isset($query['artid']) && $menuItem->query['artid'] == intval($query['artid'])) {
		unset($query['view']);

		if (isset($query['secid'])) {
			unset($query['secid']);
		}

		unset($query['artid']);

		return $segments;
	}

	if ($view == 'artlist' || $view == 'article' || $view=='author')
	{
		if (!$menuItemGiven) {
			$segments[] = $view;
		}

		unset($query['view']);

		if ($view == 'article') {
			if (isset($query['artid']) && isset($query['secid']) && $query['secid']) {
				$secid = $query['secid'];
				// Make sure we have the id and the alias
				if (strpos($query['artid'], ':') === false) {
					$db = JFactory::getDbo();
					$aquery = $db->setQuery($db->getQuery(true)
						->select('art_alias')
						->from('#__mams_articles')
						->where('artid='.(int)$query['artid'])
					);
					$alias = $db->loadResult();
					$query['artid'] = $query['artid'].':'.$alias;
				}
			} else {
				// we should have these two set for this view.  If we don't, it is an error
				return $segments;
			}
		}
		else if ($view == 'artlist') {
			if (isset($query['secid'])) {
				$secid = $query['secid'];
				if ($layout == 'author') {
					$autid = $query['autid'];
				}
				if ($layout == 'category') {
					$catid = $query['catid'];
				}
			} else {
				// we should have id set for this view.  If we don't, it is an error
				return $segments;
			}
		}
		
		if ($view == 'author') {
			if (isset($query['autid'])) {
				$secid = $query['autid'];
				unset($query['autid']);
			} else {
				// we should have id set for this view.  If we don't, it is an error
				return $segments;
			}
		}

		if ($menuItemGiven && isset($menuItem->query['secid'])) {
			$mSecid = $menuItem->query['secid'];
		} else {
			$mSecid = 0;
		}

		
		$segments[] = $secid;

		if ($view == 'article') {
			if ($advanced) {
				list($tmp, $id) = explode(':', $query['artid'], 2);
			}
			else {
				$id = $query['artid'];
			}
			$segments[] = $id;
		}
		if ($view == 'artlist') {
			if ($layout == 'author') {
				$segments[] = $autid;
				unset($query['autid']);
				unset($query['layout']);
			}
			if ($layout == 'category') {
				$segments[] = $catid;
				unset($query['catid']);
				unset($query['layout']);
			}
		}
		unset($query['artid']);
		unset($query['secid']);
	}

	// if the layout is specified and it is the same as the layout in the menu item, we
	// unset it so it doesn't go into the query string.
	if (isset($query['layout'])) {
		if ($menuItemGiven && isset($menuItem->query['layout'])) {
			if ($query['layout'] == $menuItem->query['layout']) {

				unset($query['layout']);
			}
		}
		else {
			if ($query['layout'] == 'default') {
				unset($query['layout']);
			}
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
