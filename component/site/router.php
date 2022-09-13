<?php
defined('_JEXEC') or die;

jimport('joomla.application.categories');

function MAMSBuildRoute(&$query)
{
	$items = Array();
	$foundart = 0;
	$foundartlist = 0;
	$foundsec = 0;
	$foundseclist = 0;
	$foundcat = 0;
	$foundcatsec = 0;
	$foundcatlist = 0;
	$foundtag = 0;
	$foundtagsec = 0;
	$foundtagcat = 0;
	$foundaut = 0;
	$segments = array();
	$app = JFactory::getApplication();
	$menu	= $app->getMenu();
	$items	= $menu->getItems('component', 'com_mams');
	
	if (isset($query['view'])) $view = $query['view']; else $view = "";
	if (isset($query['layout'])) $layout = $query['layout']; else $layout = "";
	if (isset($query['secid'])) $secid = $query['secid']; else $secid = 0;
	if (isset($query['catid'])) $catid = $query['catid']; else $catid = 0;
	if (isset($query['tagid'])) $tagid = $query['tagid']; else $tagid = 0;
	if (isset($query['Itemid'])) $default = $query['Itemid']; else $default = 0;

	if (!is_array($catid) && !is_array($secid) && !is_array($tagid)){
		foreach ($items as $mi) {

			// Article
			if (isset($mi->query['artid'])) {
				if (isset($query['artid'])) $artid = $query['artid']; else $artid = 0;
				if ( ! empty( $mi->query['artid'] ) && ( (int) $mi->query['artid'] == (int) $artid ) ) {
					$foundart = $mi->id;
				}
			}
			
			// Section
			if (isset($mi->query['secid'])) {
				if ( is_array( $mi->query['secid'] ) ) {
					if ( ! empty( $mi->query['secid'] ) && ( (int) $mi->query['secid'][0] == (int) $secid ) && empty( $mi->query['catid'] ) ) {
						$foundsec = $mi->id;
					}
				} else {
					if ( ! empty( $mi->query['secid'] ) && ( (int) $mi->query['secid'] == (int) $secid ) && empty( $mi->query['catid'] ) ) {
						$foundsec = $mi->id;
					}
				}
			}

			// Category and Section Category
			if (isset($mi->query['catid'])) {
				if ( is_array( $mi->query['catid'] ) ) {
					if ( ! empty( $mi->query['catid'] ) && ( (int) $mi->query['catid'][0] == (int) $catid ) && empty( $mi->query['secid'] ) ) {
						$foundcat = $mi->id;
					}

					if (isset($mi->query['secid'])) {
						if ( is_array( $mi->query['secid'] ) ) {
							if ( ! empty( $mi->query['catid'] ) && ( (int) $mi->query['catid'][0] == (int) $catid ) && ! empty( $mi->query['secid'] ) && ( (int) $mi->query['secid'][0] == (int) $secid ) ) {
								$foundcatsec = $mi->id;
							}
						} else {
							if ( ! empty( $mi->query['catid'] ) && ( (int) $mi->query['catid'][0] == (int) $catid ) && ! empty( $mi->query['secid'] ) && ( (int) $mi->query['secid'] == (int) $secid ) ) {
								$foundcatsec = $mi->id;
							}
						}
					}
				} else {
					if ( ! empty( $mi->query['catid'] ) && ( (int) $mi->query['catid'] == (int) $catid ) && empty( $mi->query['secid'] ) ) {
						$foundcat = $mi->id;
					}
					if (isset($mi->query['secid'])) {
						if ( is_array( $mi->query['secid'] ) ) {
							if ( ! empty( $mi->query['catid'] ) && ( (int) $mi->query['catid'] == (int) $catid ) && ! empty( $mi->query['secid'] ) && ( (int) $mi->query['secid'][0] == (int) $secid ) ) {
								$foundcatsec = $mi->id;
							}
						} else {
							if ( ! empty( $mi->query['catid'] ) && ( (int) $mi->query['catid'] == (int) $catid ) && ! empty( $mi->query['secid'] ) && ( (int) $mi->query['secid'] == (int) $secid ) ) {
								$foundcatsec = $mi->id;
							}
						}
					}
				}
			}

			// Tag, Tag/Category and Tag/Section
			if (isset($mi->query['tagid'])) {
				if ( is_array( $mi->query['tagid'] ) ) $mitagid = $mi->query['tagid'][0];
				else $mitagid = $mi->query['tagid'];

					// Tag
					if ( ! empty( $mi->query['tagid'] ) && ( (int) $mitagid == (int) $tagid ) && empty( $mi->query['secid'] ) && empty( $mi->query['catid'] ) ) {
						$foundtag = $mi->id;
					}

					// Tag/Section
					if (isset($mi->query['secid'])) {
						if ( is_array( $mi->query['secid'] ) ) {
							if ( ! empty( $mi->query['tagid'] ) && ( (int) $mitagid == (int) $tagid ) && ! empty( $mi->query['secid'] ) && ( (int) $mi->query['secid'][0] == (int) $secid ) ) {
								$foundtagsec = $mi->id;
							}
						} else {
							if ( ! empty( $mi->query['tagid'] ) && ( (int) $mitagid == (int) $tagid ) && ! empty( $mi->query['secid'] ) && ( (int) $mi->query['secid'] == (int) $secid ) ) {
								$foundtagsec = $mi->id;
							}
						}
					}

					// Tag/Category
					if (isset($mi->query['secid'])) {
						if ( is_array( $mi->query['secid'] ) ) {
							if ( ! empty( $mi->query['tagid'] ) && ( (int) $mitagid == (int) $tagid ) && ! empty( $mi->query['catid'] ) && ( (int) $mi->query['catid'][0] == (int) $catid ) ) {
								$foundtagcat = $mi->id;
							}
						} else {
							if ( ! empty( $mi->query['tagid'] ) && ( (int) $mitagid == (int) $tagid ) && ! empty( $mi->query['catid'] ) && ( (int) $mi->query['catid'] == (int) $catid ) ) {
								$foundtagcat = $mi->id;
							}
						}
					}
			}

			// Author Page
			if (isset($mi->query['autid'])) {
				if (isset($query['autid']))  $autid = $query['autid']; else $autid = 0;
				if ( ! empty( $mi->query['autid'] ) && ( (int) $mi->query['autid'] == (int) $autid ) ) {
					$foundaut = $mi->id;
				}
			}

			if (isset($mi->query['layout'])) {
				if ( $mi->query['layout'] == 'catlist' && $layout == 'category' ) {
					$foundcatlist = $mi->id;
				}
				if ( $mi->query['layout'] == 'seclist' && $layout == 'section' ) {
					$foundseclist = $mi->id;
				}
				if ( $mi->query['layout'] == 'allsecs' && $view == 'article' && ! $mi->home ) {
					$foundartlist = $mi->id;
				}
			}
		}
	}
	
	if ($view == 'article') {
		if ($foundart) {
			$query['Itemid'] = $foundart; 
			unset ($query['artid']);
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['secid']);
			unset ($query['catid']);
		} else if ($foundcatsec) {
			$query['Itemid'] = $foundcatsec;
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['secid']);
			unset ($query['catid']);
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
			
		} else if ($foundtagsec) {
			$query['Itemid'] = $foundtagsec;
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['secid']);
			unset ($query['catid']);
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

		} else if ($foundtagcat) {
			$query['Itemid'] = $foundtagcat;
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['secid']);
			unset ($query['catid']);
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

		} else if ($foundsec) {
			$query['Itemid'] = $foundsec;
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['secid']);
			unset ($query['catid']);
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
			
		} else if ($foundcat) {
			$query['Itemid'] = $foundcat;
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['secid']);
			unset ($query['catid']);
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
			
		} else if ($foundtag) {
			$query['Itemid'] = $foundtag;
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['secid']);
			unset ($query['catid']);
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

		} else if ($foundartlist) {
			$query['Itemid'] = $foundartlist;
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['secid']);
			unset ($query['catid']);
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
		if ($foundart) {
			$query['Itemid'] = $foundart;
			//unset ($query['view']);
			//unset ($query['autid']);
			//unset ($query['layout']);
		} elseif ($foundaut) {
			$query['Itemid'] = $foundaut;
			unset ($query['view']);
			unset ($query['autid']);
			unset ($query['layout']);
		} elseif ($foundcatsec) {
			$query['Itemid'] = $foundcatsec;
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['catid']);
			unset ($query['secid']);
			unset ($query['layout']);
		} elseif ($foundtagsec) {
			$query['Itemid'] = $foundtagsec;
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['catid']);
			unset ($query['secid']);
			unset ($query['layout']);
		} elseif ($foundtagcat) {
			$query['Itemid'] = $foundtagcat;
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['catid']);
			unset ($query['secid']);
			unset ($query['layout']);
		} elseif ($foundcat) {
			$query['Itemid'] = $foundcat;
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['catid']);
			unset ($query['secid']);
			unset ($query['layout']);
		} elseif ($foundtag) {
			$query['Itemid'] = $foundtag;
			unset ($query['view']);
			unset ($query['tagid']);
			unset ($query['catid']);
			unset ($query['secid']);
			unset ($query['layout']);
		} elseif ($foundsec) {
			$query['Itemid'] = $foundsec;
			unset ($query['view']);
			unset ($query['layout']);
			unset ($query['tagid']);
			if (isset($query['catid'])) {
				if (is_array($query['catid'])) $query['catid'] = $query['catid'][0];
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
				unset ($query['catid']);
			}
		} elseif ($foundcatlist) {
			$query['Itemid'] = $foundcatlist;
			unset ($query['view']);
			unset ($query['layout']);
			if (is_array($query['catid'])) $query['catid'] = $query['catid'][0];
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
		} elseif ($foundseclist) {
			$query['Itemid'] = $foundseclist;
			unset ($query['view']);
			unset ($query['layout']);
			if (is_array($query['secid'])) $query['secid'] = $query['secid'][0];
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
					} else {
						// check for tag
						$query = 'SELECT tag_alias, tag_id FROM #__mams_tags WHERE tag_id = '.(int)$id;
						$db->setQuery($query);
						$tag = $db->loadObject();

						if ($tag && $tag->tag_alias == $alias) {
							$vars['view'] = 'artlist';
							$vars['layout'] = 'tag';
							$vars['tagid'] = (int)$id;
							return $vars;
						}

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
				$vars['layout'] = 'catsec';
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

	return $vars;
}
