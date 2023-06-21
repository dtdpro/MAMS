<?php
defined('_JEXEC') or die();
$first = true;
if (isset($this->articles)) {
	foreach ( $this->articles as $a ) {
		//Load up the Params
		$registry = new JRegistry;
		$registry->loadString( $a->params );
		$a->params = $registry;

		// Merge menu item params with item params, item params take precedence
		$params = clone $this->params;
		$params->merge( $a->params );
		$a->params = $params;

		if ( $a->content_type == "article" ) {
			$artlink = "index.php?option=com_mams&view=article";
			if ( $a->params->get( 'article_seclock', 1 ) ) {
				$artlink .= "&secid=" . $a->art_sec . ":" . $a->sec_alias;
			}
			$artlink .= "&artid=" . $a->art_id . ":" . $a->art_alias;
			if ( $a->cats && $a->params->get( 'article_catlock', 1 ) ) {
				$artlink .= '&catid=' . $a->cats[0]->cat_id;
			}
			if ( $a->tags && $a->params->get( 'article_taglock', 1 ) ) {
				$artlink .= '&tagid=' . $a->tags[0]->tag_id;
			}
		}
		if ( $a->content_type == "section" ) {
			$artlink = "index.php?option=com_mams&view=artlist&layout=section&secid=" . $a->art_id . ":" . $a->art_alias;
		}

		echo '<div class="mams-artlist-article';
		if ( $first ) {
			echo ' mams-artlist-article-first';
			$first = false;
		}
		echo '">';

		// Generate Tags
		$tagshtml = '';
		if ($this->params->get( 'show_tags', 1 ) && $a->tags) {
			foreach ($a->tags as $t) {
				$tagshtml .= " ";
				if ($this->params->get( 'link_tags', 1 )) $tagshtml .= '<a href="' . JRoute::_( "index.php?option=com_mams&view=artlist&layout=tag&tagid=" . $t->tag_id . ":" . $t->tag_alias ) . '" class="mams-artlist-taglink">';
				$tagshtml .= '<span class="uk-badge badge badge-primary">';
				if ($t->tag_icon) $tagshtml .= '<i class="'.$t->tag_icon.'"></i>';
				$tagshtml .= $t->tag_title;
				$tagshtml .= '</span>';
				if ($this->params->get( 'link_tags', 1 )) $tagshtml .= '</a>';
			}
		}

		//Tags Above
		if ($this->params->get( 'show_tags', 1 ) && $this->params->get( 'show_tags_location', "above" ) == "above" && $a->tags) {
			echo '<div class="mams-artlist-arttags">';
			echo $tagshtml;
			echo '</div>';
		}

		//Title
		echo '<div class="mams-artlist-arttitle">';
		echo '<a href="' . JRoute::_( $artlink ) . '" class="mams-artlist-artlink">' . $a->art_title . '</a>';

		//Tags After
		if ($this->params->get( 'show_tags', 1 ) && $this->params->get( 'show_tags_location', "above" ) == "after" && $a->tags) {
			echo '<span class="mams-artlist-arttags">';
			echo $tagshtml;
			echo '</span>';
		}
		echo '</div>';

		//Tags Below
		if ($this->params->get( 'show_tags', 1 ) && $this->params->get( 'show_tags_location', "above" ) == "below" && $a->tags) {
			echo '<div class="mams-artlist-arttags">';
			echo $tagshtml;
			echo '</div>';
		}

		//Authors
		if ( isset( $a->auts )) {
			if ($this->params->get( 'show_mainauth', 1 )) {
				if ( $a->auts ) {
					echo '<div class="mams-artlist-artaut">';
					$auts = array();
					foreach ( $a->auts as $f ) {
						$auts[] = '<a href="' . JRoute::_( "index.php?option=com_mams&view=author&secid=" . $f->auth_sec . "&autid=" . $f->auth_id . ":" . $f->auth_alias ) . '" class="mams-artlist-autlink">' . $f->auth_fname . ( ( $f->auth_mi ) ? " " . $f->auth_mi : "" ) . " " . $f->auth_lname . ( ( $f->auth_titles ) ? ", " . $f->auth_titles : "" ) . '</a>';
					}
					echo implode( ", ", $auts );
					echo '</div>';
				}
			}
		}

		echo '<div class="mams-artlist-arttpdrm">';


		//Further Article Details
		echo '<div class="mams-artlist-artdetails">';

		//Thumb
		if ( $a->art_thumb && $this->params->get( 'show_thumb', 1 ) ) {
			echo '<div class="mams-artlist-artimg">';
			if ( $this->params->get( 'link_thumb', 1 ) ) {
				echo '<a href="' . JRoute::_( $artlink ) . '" class="mams-artlist-thumblink">';
			}
			echo '<img class="mams-artlist-artthumb" src="' . $a->art_thumb . '" />';
			if ( $this->params->get( 'link_thumb', 1 ) ) {
				echo '</a>';
			}
			echo '</div>';
		}
		//Article Pub info and description
		echo '<div class="mams-artlist-artinfo';
		if ( $a->art_thumb && $this->params->get( 'show_thumb', 1 ) ) {
			echo 'wt';
		}
		echo '">';
		if ( $this->params->get( 'show_pubinfo', 1 ) ) {
			echo '<div class="mams-artlist-pubinfo">';
			//Section Link
			echo '<a href="' . JRoute::_( "index.php?option=com_mams&view=artlist&layout=section&secid=" . $a->sec_id . ":" . $a->sec_alias ) . '" class="mams-artlist-seclink">' . $a->sec_name . '</a>';

			//Pub Date
			if ( $this->params->get( 'show_pubdate', 1 ) ) {
				echo ' published on <strong>';
				echo date( "F j, Y", strtotime( $a->art_publish_up ) );
				echo '</strong>';
			}

			//Cat Links
			if ( isset( $a->cats ) ) {
				if ( $a->cats ) {
					if ( $this->params->get( 'show_pubdate', 1 ) ) {
						echo ' in ';
					} else {
						echo ' - ';
					}
					echo '<em>';
					$cats = Array();
					foreach ( $a->cats as $c ) {
						if ( ! $this->params->get( 'restrictcat', 0 ) ) {
							$cats[] = '<a href="' . JRoute::_( "index.php?option=com_mams&view=artlist&layout=category&catid=" . $c->cat_id . ":" . $c->cat_alias ) . '" class="mams-artlist-catlink">' . $c->cat_title . '</a>';
						} else {
							$cats[] = '<a href="' . JRoute::_( "index.php?option=com_mams&view=artlist&layout=catsec&secid=" . $a->sec_id . ":" . $a->sec_alias . "&catid=" . $c->cat_id . ":" . $c->cat_alias ) . '" class="mams-artlist-catlink">' . $c->cat_title . '</a>';
						}
					}
					echo implode( ", ", $cats );
					echo '</em>';
				}
			}
			echo '</div>';
		}

		//Desc
		if ( $this->params->get( 'show_artlist_desc', 1 ) ) {
			echo '<div class="mams-artlist-artdesc">';
			echo $a->art_desc;
			echo '</div>';
		}

		//Additional Fields
		if ( isset( $a->fields ) ) {
			if ( $a->fields ) {
				echo '<div class="mams-artlist-artfields">';
				$curgroup = "";
				$first    = true;
				foreach ( $a->fields as $f ) {
					if ($f->field_type == "auths" && !$this->params->get( 'show_mainauth', 1 )) {
						continue;
					}
					$fn = $f->field_name;
					if ( $a->art_fielddata->$fn || property_exists($f,'data') ) {
						if ( $f->group_name != $curgroup ) {
							if ( ! $first ) {
								echo '</div>';
							} else {
								$first = false;
							}
							echo '<div class="mams-artlist-' . $f->group_name . '">';
							$curgroup = $f->group_name;
							if ( $f->group_show_title ) {
								echo '<div class="mams-artlist-' . $f->group_name . '-title">';
								if ( $f->group_params->linktitlelist ) {
									echo '<a href="' . JRoute::_( "index.php?option=com_mams&view=article&secid=" . $a->sec_id . ":" . $a->sec_alias . "&artid=" . $a->art_id . ':' . $a->art_alias ) . '#' . $f->group_name . '">';
								}
								echo $f->group_title;
								if ( $f->group_params->linktitlelist ) {
									echo '</a>';
								}
								echo '</div>';
							}
						}
						if ( $f->field_params->show_title_desc ) {
							echo '<div class="mams-artlist-' . $f->group_name . '-' . $f->field_name . '-title">';
							echo $f->field_title;
							echo '</div>';
						}
						echo '<div class="mams-artlist-' . $f->group_name . '-' . $f->field_name . '">';
						if ( $f->field_params->pretext ) {
							echo $f->field_params->pretext . ' ';
						}
						switch ( $f->field_type ) {
							case "textfield":
								if ( $f->field_params->linktext == 1 ) {
									echo '<a href="' . JRoute::_( "index.php?option=com_mams&view=article&secid=" . $a->sec_id . ":" . $a->sec_alias . "&artid=" . $a->art_id . ':' . $a->art_alias ) . '#' . $f->field_name . '">';
								}
								if ( $f->field_params->linktext == 2 ) {
									echo '<a href="' . JRoute::_( "index.php?option=com_mams&view=article&secid=" . $a->sec_id . ":" . $a->sec_alias . "&artid=" . $a->art_id . ':' . $a->art_alias ) . '#' . $f->group_name . '">';
								}
								echo $a->art_fielddata->$fn;
								if ( $f->field_params->linktext ) {
									echo '</a>';
								}
								break;
							case "textbox":
							case "editor":
								echo $a->art_fielddata->$fn;
								break;
							case "auths":
								$auts = Array();
								foreach ( $f->data as $d ) {
									$auts[] = '<a href="' . JRoute::_( "index.php?option=com_mams&view=author&secid=" . $d->auth_sec . "&autid=" . $d->auth_id . ":" . $d->auth_alias ) . '" class="mams-artlist-' . $f->group_name . '-' . $f->field_name . '-autlink">' . $d->auth_fname . ( ( $d->auth_mi ) ? " " . $d->auth_mi : "" ) . " " . $d->auth_lname . ( ( $d->auth_titles ) ? ", " . $d->auth_titles : "" ) . '</a>';
								}
								echo implode( ", ", $auts );
								break;
							case "dloads":
								$firstdl = true;
								foreach ( $f->data as $d ) {
									echo '<div class="mams-artlist-' . $f->group_name . '-' . $f->field_name . '-dload">';
									echo '<a href="' . JRoute::_( "components/com_mams/dl.php?dlid=" . $d->dl_id ) . '" ';
									echo 'target="_blank" ';
									echo 'class="mams-artlist-' . $f->group_name . '-' . $f->field_name . '-artdload';
									if ( $firstdl ) {
										echo ' firstdload';
										$firstdl = false;
									}
									echo '">';
									echo 'Download ' . $d->dl_lname;
									echo '</a>';
									echo '</div>';
								}
								break;
							case "links":
								$firstlink = true;
								foreach ( $f->data as $d ) {
									echo '<div class="mams-artlist-' . $f->group_name . '-' . $f->field_name . '-link">';
									echo '<a href="' . $d->link_url . '" ';
									echo 'target="' . $d->link_target . '" ';
									echo 'class="mams-artlist-' . $f->group_name . '-' . $f->field_name . '-artlink';
									if ( $firstlink ) {
										echo ' firstlink';
										$firstlink = false;
									}
									echo '">';
									echo $d->link_title;
									echo '</a>';
									echo '</div>';
								}
								break;
						}
						if ( $f->field_params->posttext ) {
							echo ' ' . $f->field_params->posttext;
						}
						echo '</div>';
					}
				}
				if ( ! $first ) {
					echo '</div>';
				}
				echo '</div>';
			}
		}
		echo '</div>';

		//Read More
		if ( $this->params->get( 'show_readmore', 1 ) ) {
			echo '<div class="mams-artlist-artreadmore">';
			echo '<a href="' . JRoute::_( $artlink ) . '" class="mams-artlist-artlink read-more uk-button btn btn-default">';
			echo $this->params->get( 'readmore_text', "Read More" );
			echo '</a>';
			echo '</div>';
		}
		echo '</div>';


		echo '</div>';

		echo '</div>';
		echo '<div class="mams-artlist-seperator"></div>';

	}
}
if ($this->pagination) {
    echo '<div class="mams-artlist-pagination">';
    echo '<div class="mams-artlist-pagination-links">';
    echo $this->pagination->getPagesLinks();
    echo '</div>';
    echo '<div class="mams-artlist-pagination-pages">';
    echo $this->pagination->getPagesCounter() . '<br />' . $this->pagination->getResultsCounter();
    echo '</div>';
    echo '</div>';
}