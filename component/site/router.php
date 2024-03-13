<?php
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\RulesInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Router\Router;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

jimport('joomla.application.categories');

class MAMSRouter extends RouterView
{
	public function __construct($app = null, $menu = null)
	{
		$params = JComponentHelper::getParams('com_mams');

		parent::__construct($app, $menu);

		$this->attachRule(new MAMSRules($this));

		// needed for Joomla 4
		$router = $app::getRouter();
		$router->attachParseRule([$this, 'parseProcessAfter'], Router::PROCESS_AFTER);
	}

	/**
	 * Parse method for URLs
	 *
	 * @param   array  &$segments  Array of URL string-segments
	 *
	 * @return  array  Associative array of query values
	 *
	 * @since   3.5
	 */
	public function parse(&$segments)
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

			return $this->processParse($segments, $vars);
		}

		// if there is only one segment, then it points to either an article, section, or author
		// we test it first to see if it is a section.  If the id and alias match a section
		// then we assume it is a section.  If they don't we check for article, etc...
		if ($count == 1) {
			// we check fopr the : separator, if there is none assume article
			if (strpos($segments[0], ':') === false) {
                // Check if just an article alias is being used
                $query = 'SELECT art_alias, art_id, art_sec FROM #__mams_articles WHERE art_alias = "'.$db->escape($segments[0]).'"';
                $db->setQuery($query);
                $article = $db->loadObject();
                if ($article) {
                    $vars['view'] = 'article';
                    $vars['secid'] = (int)$article->art_sec;
                    $vars['artid'] = (int)$article->art_id;
                    return $this->processParse($segments, $vars);
                } else {
                    // if no article covert to integer, this is an old link with a - instaed of a :
                    $vars['view'] = 'article';
                    $vars['artid'] = (int)$segments[0];
                    return $this->processParse($segments, $vars);
                }
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

				return $this->processParse($segments, $vars);
			} else {
				// check for aricle
				$query = 'SELECT art_alias, art_id, art_sec FROM #__mams_articles WHERE art_id = '.(int)$id;
				$db->setQuery($query);
				$article = $db->loadObject();

				if ($article && $article->art_alias == $alias) {
					$vars['view'] = 'article';
					$vars['secid'] = (int)$article->art_sec;
					$vars['artid'] = (int)$id;

					return $this->processParse($segments, $vars);
				} else {
					// check for author
					$query = 'SELECT auth_alias, auth_id FROM #__mams_authors WHERE auth_id = '.(int)$id;
					$db->setQuery($query);
					$author = $db->loadObject();

					if ($author && $author->auth_alias == $alias) {
						$vars['view'] = 'author';
						$vars['autid'] = (int)$id;
						return $this->processParse($segments, $vars);
					} else {
						// check for category
						$query = 'SELECT cat_alias, cat_id FROM #__mams_cats WHERE cat_id = '.(int)$id;
						$db->setQuery($query);
						$cat = $db->loadObject();

						if ($cat && $cat->cat_alias == $alias) {
							$vars['view'] = 'artlist';
							$vars['layout'] = 'category';
							$vars['catid'] = (int)$id;
							return $this->processParse($segments, $vars);
						} else {
							// check for tag
							$query = 'SELECT tag_alias, tag_id FROM #__mams_tags WHERE tag_id = '.(int)$id;
							$db->setQuery($query);
							$tag = $db->loadObject();

							if ($tag && $tag->tag_alias == $alias) {
								$vars['view'] = 'artlist';
								$vars['layout'] = 'tag';
								$vars['tagid'] = (int)$id;
								return $this->processParse($segments, $vars);
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

					return $this->processParse($segments, $vars);
				} else {
					// check for aricle
					$query = 'SELECT art_alias, art_id, art_sec FROM #__mams_articles WHERE art_id = '.(int)$id;
					$db->setQuery($query);
					$article = $db->loadObject();

					if ($article && $article->art_alias == $alias) {
						$vars['view'] = 'article';
						$vars['secid'] = (int)$article->art_sec;
						$vars['artid'] = (int)$id;

						return $this->processParse($segments, $vars);
					} else {
						// check for author
						$query = 'SELECT auth_alias, auth_id FROM #__mams_authors WHERE auth_id = '.(int)$id;
						$db->setQuery($query);
						$author = $db->loadObject();

						if ($author && $author->auth_alias == $alias) {
							$vars['view'] = 'artlist';
							$vars['layout'] = 'author';
							$vars['autid'] = (int)$id;
							return $this->processParse($segments, $vars);
						}

					}
				}
				$vars['secid'] = $sec_id;
			} else {
				$vars['view'] = 'artlist';
				$vars['secid'] = $sec_id;
			}

			return $this->processParse($segments, $vars);
		}

		return $this->processParse($segments, $vars);
	}

	private function processParse($segments, $vars) {

		// Process the parsed variables based on custom defined rules
		foreach ($this->rules as $rule)
		{
			$rule->parse($segments, $vars);
		}

		return $vars;
	}

	/**
	 * @param Router $router
	 * @param Uri    $uri
	 *
	 * @return void
	 */
	public function parseProcessAfter(Router $router, Uri $uri)
	{
		// Kinda crazy but needed in Joomla 4
		$uri->setPath(null);
	}

}

class MAMSRules implements RulesInterface
{
	public function __construct($router)
	{
		$this->router = $router;
	}

	public function preprocess(&$query)
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
		$menu	= $this->router->menu;
		$items	= $menu->getItems('component', 'com_mams');

		if (isset($query['view'])) $view = $query['view']; else $view = "";
		if (isset($query['layout'])) $layout = $query['layout']; else $layout = "";
		if (isset($query['artid'])) $artid = $query['artid']; else $artid = 0;
		if (isset($query['secid'])) $secid = $query['secid']; else $secid = 0;
		if (isset($query['catid'])) $catid = $query['catid']; else $catid = 0;
		if (isset($query['tagid'])) $tagid = $query['tagid']; else $tagid = 0;
		if (isset($query['Itemid'])) $default = $query['Itemid']; else $default = 0;

		if (is_array($secid)) $secid = $secid[0];
		if (is_array($catid)) $catid = $catid[0];
		if (is_array($tagid)) $tagid = $tagid[0];

		foreach ($items as $mi) {

			// Article
			if (isset($mi->query['artid']) && $artid != 0) {
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

		if ($view == 'article') {
			if ($foundart != 0) {
				$query['Itemid'] = $foundart;
			} else if ($foundcatsec != 0) {
				$query['Itemid'] = $foundcatsec;
			} else if ($foundtagsec != 0) {
				$query['Itemid'] = $foundtagsec;
			} else if ($foundtagcat != 0) {
				$query['Itemid'] = $foundtagcat;
			} else if ($foundsec != 0) {
				$query['Itemid'] = $foundsec;
			} else if ($foundcat != 0) {
				$query['Itemid'] = $foundcat;
			} else if ($foundtag != 0) {
				$query['Itemid'] = $foundtag;
			} else if ($foundartlist != 0) {
				$query['Itemid'] = $foundartlist;
			} else {
				$query['Itemid'] = $default;
			}
		} else if ($view == 'artlist') {
			if ($foundart != 0) {
				$query['Itemid'] = $foundart;
			} elseif ($foundaut != 0) {
				$query['Itemid'] = $foundaut;
			} elseif ($foundcatsec != 0) {
				$query['Itemid'] = $foundcatsec;
			} elseif ($foundtagsec != 0) {
				$query['Itemid'] = $foundtagsec;
			} elseif ($foundtagcat != 0) {
				$query['Itemid'] = $foundtagcat;
			} elseif ($foundcat != 0) {
				$query['Itemid'] = $foundcat;
			} elseif ($foundtag != 0) {
				$query['Itemid'] = $foundtag;
			} elseif ($foundsec != 0) {
				$query['Itemid'] = $foundsec;
			} elseif ($foundcatlist != 0) {
				$query['Itemid'] = $foundcatlist;
			} elseif ($foundseclist != 0) {
				$query['Itemid'] = $foundseclist;
			}
		} else if ($view == 'author') {
			if ($foundaut != 0) {
				$query['Itemid'] = $foundaut;
			} else if ($foundsec != 0) {
				$query['Itemid'] = $foundsec;
			} else {
				$query['Itemid'] = $default;
			}
		}
	}

	public function build(&$query, &$segments)
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
		$menu	= $this->router->menu;
		$items	= $menu->getItems('component', 'com_mams');

        $params      = ComponentHelper::getParams('com_mams');
        $noIDs = (bool)  $params->get('sef_ids');


		if (isset($query['view'])) $view = $query['view']; else $view = "";
		if (isset($query['layout'])) $layout = $query['layout']; else $layout = "";
		if (isset($query['artid'])) $artid = $query['artid']; else $artid = 0;
		if (isset($query['secid'])) $secid = $query['secid']; else $secid = 0;
		if (isset($query['catid'])) $catid = $query['catid']; else $catid = 0;
		if (isset($query['tagid'])) $tagid = $query['tagid']; else $tagid = 0;
		if (isset($query['Itemid'])) $default = $query['Itemid']; else $default = 0;

		if (is_array($secid)) $secid = $secid[0];
		if (is_array($catid)) $catid = $catid[0];
		if (is_array($tagid)) $tagid = $tagid[0];

		foreach ($items as $mi) {

			// Article
			if (isset($mi->query['artid']) && $artid != 0) {
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

		if ($view == 'article') {
			if ($foundart != 0) {
				unset ($query['view'],$query['artid'],$query['tagid'],$query['secid'],$query['catid']);
			} else {
				unset ($query['view'],$query['tagid'],$query['secid'],$query['catid']);
                if (strpos($query['artid'], ':')) {
                    if ($noIDs) {
                        $parts = explode(':', $query['artid']);
                        $query['artid'] = $parts[1];
                    }
                } else if (is_int($query['artid'])) {
                    $db = JFactory::getDbo();
                    $aquery = $db->setQuery($db->getQuery(true)
                        ->select('art_alias')
                        ->from('#__mams_articles')
                        ->where('art_id='.(int)$query['artid'])
                    );
                    $alias = $db->loadResult();
                    if ($noIDs) {
                        $query['artid'] = $alias;
                    } else {
                        $query['artid'] = $query['artid'].':'.$alias;
                    }
                }
                $segments[] = $query['artid'];
                unset ($query['artid']);
			}
		} else if ($view == 'artlist') {
			if ($foundart != 0) {
				//unset ($query['view'],$query['autid'],$query['layout']);
			} elseif ($foundaut != 0) {
				unset ($query['view'],$query['secid'],$query['autid'],$query['layout']);
			} elseif ($foundcatsec != 0) {
				unset ($query['view'],$query['tagid'],$query['catid'],$query['secid'],$query['layout']);
			} elseif ($foundtagsec != 0) {
				unset ($query['view'],$query['tagid'],$query['catid'],$query['secid'],$query['layout']);
			} elseif ($foundtagcat != 0) {
				unset ($query['view'],$query['tagid'],$query['catid'],$query['secid'],$query['layout']);
			} elseif ($foundcat != 0) {
				unset ($query['view'],$query['tagid'],$query['catid'],$query['secid'],$query['layout']);
			} elseif ($foundtag != 0) {
				unset ($query['view'],$query['tagid'],$query['catid'],$query['secid'],$query['layout']);
			} elseif ($foundsec != 0) {
				unset ($query['view'],$query['layout'],$query['tagid'],$query['secid'],$query['catid']);
				if ($catid != 0) {
					if (strpos($catid, ':') === false) {
						$db = JFactory::getDbo();
						$aquery = $db->setQuery($db->getQuery(true)
						                           ->select('cat_alias')
						                           ->from('#__mams_cats')
						                           ->where('cat_id='.(int)$catid)
						);
						$alias = $db->loadResult();
						$catQuery = $catid.':'.$alias;
						$segments[] = $catQuery;
					} else {
						$segments[] = $catid;
					}
				}
			} elseif ($foundcatlist != 0) {
				unset ($query['view'],$query['layout'],$query['catid']);
				if (strpos($catid, ':') === false) {
					$db = JFactory::getDbo();
					$aquery = $db->setQuery($db->getQuery(true)
					                           ->select('cat_alias')
					                           ->from('#__mams_cats')
					                           ->where('cat_id='.$catid)
					);
					$alias = $db->loadResult();
					$catQuery = $catid.':'.$alias;
					$segments[] = $catQuery;
				} else {
					$segments[] = $catid;
				}
			} elseif ($foundseclist != 0) {
				unset ($query['view'],$query['layout'],$query['secid']);
				if (strpos($secid, ':') === false) {
					$db = JFactory::getDbo();
					$aquery = $db->setQuery($db->getQuery(true)
					                           ->select('sec_alias')
					                           ->from('#__mams_secs')
					                           ->where('sec_id='.$secid)
					);
					$alias = $db->loadResult();
					$secQuery = $secid.':'.$alias;
					$segments[] = $secQuery;
				} else {
					$segments[] = $secid;
				}
			} else {
				unset ($query['view'],$query['layout']);
			}
		} else if ($view == 'author') {
			if ($foundaut != 0) {
				unset ($query['view'],$query['secid']);
			} else if ($foundsec != 0) {
				unset ($query['view'],$query['secid'],$query['layout']);
				if ($autid === false && isset($query['autid'])) {
					$db = JFactory::getDbo();
					$aquery = $db->setQuery($db->getQuery(true)
					                           ->select('auth_alias')
					                           ->from('#__mams_authors')
					                           ->where('auth_id='.(int)$query['autid'])
					);
					$alias = $db->loadResult();
					$autQuery = $autid.':'.$alias;
					$segments[] = $autQuery;
				} else {
					$segments[] = $autid;
				}
			} else {
				unset ($query['view'],$query['layout']);
			}
		}

	}

	public function parse(&$segments, &$vars)
	{

	}
}





function MAMSBuildRoute(&$query)
{
	$app = JFactory::getApplication();
	$router = new MAMSRouter($app, $app->getMenu());

	return $router->build($query);
}

function MAMSParseRoute($segments)
{
	$app = JFactory::getApplication();
	$router = new MAMSRouter($app, $app->getMenu());

	return $router->parse($segments);
}
