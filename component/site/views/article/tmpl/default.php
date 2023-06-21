<?php
defined('_JEXEC') or die();
if ($this->params->get('divwrapper',1)) {
	echo '<div id="system" class="'.$this->params->get('wrapperclass','uk-article').'">';
}
$config=MAMSHelper::getConfig();
$user=JFactory::getUser();

echo $this->beforeDisplayContent;

if ($user->id) {
	$rurl = JRoute::_('index.php?option=com_mams&view=artlist&secid='.$this->article->art_sec);
	$msg = $config->noaccessmsg;
}
else {
	$rurl = JRoute::_('index.php?option=com_users&view=login&return='.$this->returnUrl);
	$msg = $config->loginmsg;
}

//Fields Renderer
if ($this->article->fields) {
	$curgroup = "";
	$first=true;
	foreach ($this->article->fields as $f) {
		if ($f->field_type == "related" && (!$this->params->get('show_related',1) || !$this->related)) { continue; }
		$fn = $f->field_name;
		$gns = 0;

		if ($f->group_name == "article") {
		    $gns = 1;
        } else {
		    $gnsv = "show_".$f->group_name;
		    if (isset($this->article->art_fielddata->$gnsv)) $gns = $this->article->art_fielddata->$gnsv;
		    else $gns = 0;
        }

        // Start Group and/or end previous group
        if ($f->group_name != $curgroup && $gns) {
			if (!$first) { echo '<div style="clear:both"></div>'; echo '</div>';  }
			else { $first=false; }
			echo '<div class="mams-article-'.$f->group_name.'">';
			$curgroup = $f->group_name;
			echo '<a name="'.$f->group_name.'"></a>';
			if ($f->group_show_title) {
				echo '<div class="mams-article-'.$f->group_name.'-title">';
				echo $f->group_title;
				echo '</div>';
			}
		}

        $has_content = false;
        switch ($f->field_type) {
	        case "title":
		        if ($this->article->art_title) {
			        $has_content = true;
		        }
		        break;
	        case "artauthedlink":
	        case "rendera":
	        case "module":
	            $has_content = true;
		        break;
            case "body":
                if ($this->article->art_content) {
                   $has_content = true;
                }
                break;
	        case "tags":
		        if ($this->article->tags) {
			        $has_content = true;
		        }
		        break;
            case "textfield":
            case "textbox":
            case "editor":
                if (isset($this->article->art_fielddata->$fn)) {
                    if ($this->article->art_fielddata->$fn) $has_content = true;
                }
                break;
            case 'pubinfo':
                if ($this->params->get('show_pubinfo', 1)) {
                    $has_content = true;
                }
                break;
            case "auths":
            case "dloads":
            case "images":
            case "links":
            case "media":
                if ($f->data) {
                    $has_content = true;
                }
                if ($this->params->get('show_mainauth',1) == 0 && $f->field_type == "auths") {
	                $has_content = false;
                }
                break;
            case 'related':
                if ($this->params->get('show_related',1) && $this->related) {
                    $has_content = true;
                }
                break;
        }

        // Group set to show or named article
        if ($gns && $has_content) {

            // Start Field
            echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name;
            if (property_exists($f->params,'additional_css')) echo " ".$f->params->additional_css;
            echo '">';
            echo '<a name="'.$f->group_name.'-'.$f->field_name.'"></a>';
            if ($f->params->show_title_page && $gns) {
                echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-title">';
                echo $f->field_title;
                echo '</div>';
            }

			switch ($f->field_type) {
				case "module":
					echo JHtml::_('content.prepare', '{loadposition '.$f->field_rssname.',html5}');
					break;
				case "rendera":
					echo $this->article->rendera;
					break;
				case "title":
					if ($this->params->get("show_page_heading",1)) {
						if ( $this->article->art_showtitle ) {
							echo '<h1 class="title uk-article-title">';
							echo $this->article->art_title;
							echo '</h1>';
						}
					}
					break;
                case "body":
                    echo '<div class="mams-article-content">';
                    echo $this->article->art_content;
                    echo '</div>';
                    break;
                case "textfield":
                case "textbox":
                case "editor":
                    echo $this->article->art_fielddata->$fn;
                    break;
                case 'pubinfo':
                    if ($this->params->get('show_pubinfo', 1)) {
                        //Pub Info
                        echo '<div class="mams-article-pubinfo">';

                        //Section Link
                        echo '<a href="' . JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=" . $this->article->sec_id . ":" . $this->article->sec_alias) . '" class="mams-article-seclink">' . $this->article->sec_name . '</a>';

                        //Pub Date
                        if ($this->params->get('show_pubdate', 1)) {
                            echo ' published on <strong>';
                            echo date("F j, Y", strtotime($this->article->art_publish_up));
                            echo '</strong>';
                        }

                        //Cat Links
                        if ($this->article->cats) {
                            if ($this->params->get('show_pubdate', 1)) {
                                echo ' in <em>';
                            } else {
                                echo ' - <em>';
                            }
                            $cats = array();
                            foreach ($this->article->cats as $c) {
                                if (!$this->params->get('restrictcat', 0)) {$cats[] = '<a href="' . JRoute::_("index.php?option=com_mams&view=artlist&layout=category&catid=" . $c->cat_id . ":" . $c->cat_alias) . '" class="mams-article-catlink">' . $c->cat_title . '</a>';}
                                else {$cats[] = '<a href="' . JRoute::_("index.php?option=com_mams&view=artlist&layout=catsec&secid=" . $this->article->sec_id . ":" . $this->article->sec_alias . "&catid=" . $c->cat_id . ":" . $c->cat_alias) . '" class="mams-article-catlink">' . $c->cat_title . '</a>';}
                            }
                            echo implode(", ", $cats);
                            echo '</em>';
                        }
                        echo '</div>';
                    }
                    break;
				case "tags":
					foreach ($this->article->tags as $t) {
						if ($this->params->get( 'link_tags', 1 )) {echo '<a href="' . JRoute::_( "index.php?option=com_mams&view=artlist&layout=tag&tagid=" . $t->tag_id . ":" . $t->tag_alias ) . '" class="mams-artlist-taglink">';}
						echo '<span class="uk-badge badge badge-primary mams-article-'.$f->group_name.'-'.$f->field_name.'-link mams-article-tag">';
						if ($t->tag_icon) {echo '<i class="'.$t->tag_icon.'"></i> ';}
 						echo $t->tag_title;
						echo '</span> ';
						if ($this->params->get( 'link_tags', 1 )) {echo '</a>';}
					}
					break;
				case "auths":
					$auths = $f->data;
					$authbyrow=$this->params->get('auth_byrow',2);
					$authcount=0;

					// Begin Row Div
					echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-authrow mams-article-authrow uk-grid uk-grid-medium" uk-grid>';

					foreach ($auths as $d) {
						// Begin Outer Div
						echo '<div class="uk-width-1-'. $authbyrow .'@s uk-width-1-1 uk-width-small-1-'. $authbyrow .'">';

						// Begin Inner Div
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-auth mams-article-auth uk-grid-small uk-grid" uk-grid>';

						// Author Image
						if (!(empty($d->auth_image)) && $this->params->get('show_authimg',0)) {
							echo '<div class="uk-width-1-4@l uk-width-1-3@m uk-width-1-4@s uk-width-1-5">';
							echo '<div class="mams-article-auth-image"><img src="'. $d->auth_image .'" /></div>';
							echo '</div>';
						}

						// Begin Details Div
						echo '<div';
						if (!(empty($d->auth_image)) && $this->params->get('show_authimg',0))  {
							echo ' class="uk-align-middle uk-width-3-4@l uk-width-2-3@m uk-width-3-4@s uk-width-4-5"';
						}
						echo '>';

						// Author Name
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-auth-name mams-article-auth-name">';
						echo '<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$d->auth_sec."&autid=".$d->auth_id.":".$d->auth_alias).'" class="mams-article-'.$f->group_name.'-'.$f->field_name.'-autlink">';
						echo $d->auth_fname.(($d->auth_mi) ? " ".$d->auth_mi : "")." ".$d->auth_lname.(($d->auth_titles) ? ", ".$d->auth_titles : "").'</a>';
						echo '</div>';

						// Author Credentials
						if ($this->params->get('show_authcred',1)) {
							echo '<div class="mams-article-' . $f->group_name . '-' . $f->field_name . '-auth-cred mams-article-auth-cred">';
							echo $d->auth_credentials;
							echo '</div>';
						}

						// End Details Div
						echo '</div>';

						// End Inner Div
						echo '</div>';

						// End Outer Div
						echo '</div>';

						// Increment authors in row count
						$authcount++;

						// Reset Row div when column number met
						if ($authcount == $authbyrow) {
							// End Row Div
							echo '</div>';

							// Begin Row Div
							echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-authrow mams-article-authrow uk-grid uk-grid-medium" uk-grid>';

							// Reset authors in row count
							$authcount=0;
						}

					}

					// End Row Div
					echo '</div>';

					break;

				case "dloads":
					$dloads = $f->data;
					$firstdl=true;
					foreach ($dloads as $d) {
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-dload">';
						if (in_array($d->access,$user->getAuthorisedViewLevels())) {echo '<a href="'.JRoute::_("components/com_mams/dl.php?dlid=".$d->dl_id).'" target="_blank" ';}
						else {echo '<a href="'.JRoute::_($rurl).'" ';}
						echo 'class="mams-article-'.$f->group_name.'-'.$f->field_name.'-artdload mams-article-dllink uk-button btn btn-default';
						if ($firstdl) { echo ' firstdload'; $firstdl=false; }
						echo '">';
						echo 'Download '.$d->dl_lname;
						echo '</a>';
						echo '</div>';
					}
					break;
				case "images":
					$images = $f->data;
					$firstimg=true;
					foreach ($images as $i) {
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-image';
						if ($firstimg) { echo ' firstimage'; $firstimg=false; }
						echo '">';
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-image-img">';
						if (in_array($i->access,$user->getAuthorisedViewLevels())) {echo '<a href="'.$i->img_full.'" target="_blank" ';}
						else {echo '<a href="'.JRoute::_($rurl).'" ';}
						echo 'data-lightbox="group:'.$f->group_name.'-'.$f->field_name.'-images" class="mams-article-'.$f->group_name.'-'.$f->field_name.'-artimage mams-article-imglink';
						echo '">';
						echo '<img src="'.$i->img_thumb.'" class="mams-article-imgthumb">';
						echo '</a></div>';
						if ($this->params->get('show_imgtitle',1) || $this->params->get('show_imgdetails',1)) {
							echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-image-info">';
							if ($this->params->get('show_imgtitle',1)) {
								echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-image-title">';
								if (in_array($i->access,$user->getAuthorisedViewLevels())) {echo '<a href="'.$i->img_full.'" target="_blank" ';}
								else {echo '<a href="'.JRoute::_($rurl).'" ';}
								echo 'data-lightbox="group:'.$f->group_name.'-'.$f->field_name.'-links" class="mams-article-'.$f->group_name.'-'.$f->field_name.'-imglink';
								echo '">';
								echo $i->img_exttitle;
								echo '</a></div>';
							}
							if ($this->params->get('show_imgdetails',1)) {
								echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-image-details">';
								echo $i->img_desc;
								echo '</div>';
							}
							echo '</div>';
						}
						echo '</div>';
					}
					break;
				case "artauthedlink":
					if (count($this->article->auts) > 0) {
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-link mams-article-link">';
						echo '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=artauthed&artid=".$this->article->art_id).'" ';
						echo 'class="mams-article-'.$f->group_name.'-'.$f->field_name.'-artlink uk-button uk-button-small uk-button-default';
						echo '">';
						if (count($this->article->auts) > 1) { echo 'View additional articles by these authors'; }
					    else { echo 'View additional articles by this author';}
						echo '</a>';
						echo '</div>';
                    }
					break;
				case "links":
					$links = $f->data;
					$firstlink=true;
					foreach ($links as $d) {
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-link mams-article-link">';
						echo '<a href="'.$d->link_url.'" ';
						echo 'target="'.$d->link_target.'" ';
						echo 'class="mams-article-'.$f->group_name.'-'.$f->field_name.'-artlink';
						if ($firstlink) { echo ' firstlink'; $firstlink=false; }
						echo '">';
						echo $d->link_title;
						echo '</a>';
						echo '</div>';
					}
					break;
				case "media":
					$media = $f->data;
					$multiplePlayers = $config->player_multiple;
					if ($multiplePlayers) {
					    foreach ($media as $m) {
						    if ($config->show_media_title) {
							    echo '<div class="mams-article-mediatitle">'.$m->med_exttitle.'</div>';
						    }
                            echo '<div align="center">'; //Center the player, enoyingly needed
                            echo '<div class="mams-article-mediawrap"';
                            if ($config->player_fixed) echo ' style="width: '.(int)$config->vid_w.'px;"';
                            echo '>';

						    //Video Player
                            if ($m->med_type == 'vid' || $m->med_type == 'vids') {
                                echo '<div class="mams-article-media-player';
                                echo 'one';
                                echo '">';
                                echo '<video width="'.(int)$config->vid_w.'" height="'.(int)$config->vid_h.'" ';
                                if (!$config->player_fixed) echo 'style="width: 100%; height: 100%;" ';
                                echo 'id="mams-article-mediaelement-'.$f->field_name.'-'.$m->med_id.'" src="';
                                if ($config->vid_https) echo 'https://';
                                else echo 'http://';
                                echo $config->vid5_url.'/'.$m->med_file.'" type="video/mp4" controls="controls" poster="'.$m->med_still.'"';
                                if ($m->med_autoplay) echo ' autoplay="autoplay"';
                                echo '></video>';
                                echo '<script type="text/javascript">';
                                echo "var fmplayer_".$m->med_id." = new MediaElementPlayer('#mams-article-mediaelement-".$f->field_name."-".$m->med_id."',{features: ['playpause','current','progress','duration','volume','fullscreen','mamsanalytics'";
                                if ($config->gapro) echo ",'mamsgoogleanalytics'";
                                echo "], enablePluginSmoothing: true, trackId: ".$this->article->track_id.", videoId: ".$m->med_id.", videoExtTitle: '".addslashes($m->med_exttitle)."', videoIntTitle: '".addslashes($m->med_inttitle)."'});";
                                echo '</script>';
                                echo '</div>';
                            }

                            //Audio Player
                            if ($m->med_type == 'aud') {
                                echo '<div class="mams-article-media-player';
                                echo 'one';
                                echo '">';
                                echo '<audio id="mams-article-mediaelement-'.$f->field_name.'-'.$m->med_id.'" width="'.(int)$config->vid_w.'" ';
                                if (!$config->player_fixed) echo 'style="width: 100%;" ';
                                echo 'src="'.JURI::base( true ).'/'.$m->med_file.'" type="audio/mp3" controls="controls"></audio>';
                                echo '<script type="text/javascript">';
                                echo "var fmplayer_".$m->med_id." = new MediaElementPlayer('#mams-article-mediaelement-".$f->field_name."-".$m->med_id."',{features: ['playpause','current','progress','duration','volume','mamsanalytics'";
                                if ($config->gapro) echo ",'mamsgoogleanalytics'";
                                echo "], trackId: ".$this->article->track_id.", videoId: ".$m->med_id.", videoExtTitle: '".addslashes($m->med_exttitle)."', videoIntTitle: '".addslashes($m->med_inttitle)."'});";
                                echo '</script>';
                                echo '</div>';
                            }

						    //Audio Player - CDN
						    if ($m->med_type == 'auds') {
							    echo '<div class="mams-article-media-player';
							    echo 'one';
							    echo '">';
							    echo '<audio id="mams-article-mediaelement-'.$f->field_name.'-'.$m->med_id.'" width="'.(int)$config->vid_w.'" ';
							    if (!$config->player_fixed) echo 'style="width: 100%;" src="';
							    if ($config->vid_https) echo 'https://';
							    else echo 'http://';
							    echo $config->vid5_url.'/'.$m->med_file.'" type="audio/mp3" controls="controls"></audio>';
							    echo '<script type="text/javascript">';
							    echo "var fmplayer_".$m->med_id." = new MediaElementPlayer('#mams-article-mediaelement-".$f->field_name."-".$m->med_id."',{features: ['playpause','current','progress','duration','volume','mamsanalytics'";
							    if ($config->gapro) echo ",'mamsgoogleanalytics'";
							    echo "], trackId: ".$this->article->track_id.", videoId: ".$m->med_id.", videoExtTitle: '".addslashes($m->med_exttitle)."', videoIntTitle: '".addslashes($m->med_inttitle)."'});";
							    echo '</script>';
							    echo '</div>';
						    }

                            echo '</div>';
                            echo '<div style="clear:both"></div>';
                            echo '</div>';
                        }
                    } else {
						echo '<div align="center">'; //Center the player, enoyingly needed
						echo '<div class="mams-article-mediawrap"';
						if ($config->player_fixed) echo ' style="width: '.(int)$config->vid_w.'px;"';
						echo '>';
						//Video Player
						if ($media[0]->med_type == 'vid' || $media[0]->med_type == 'vids') {
							echo '<div class="mams-article-media-player';
							if (count($media) == 1) echo 'one';
							else if ($config->player_fixed) echo 'fixed';
							echo '">';
							echo '<video width="'.(int)$config->vid_w.'" height="'.(int)$config->vid_h.'" ';
							if (!$config->player_fixed) echo 'style="width: 100%; height: 100%;" ';
							echo 'id="mams-article-mediaelement-'.$f->field_name.'" src="';
							if ($config->vid_https) $vidprotocol = 'https://';
							else $vidprotocol = 'http://';
                            echo $vidprotocol;
							echo $config->vid5_url.'/'.$media[0]->med_file.'" type="video/mp4" controls="controls" poster="'.$media[0]->med_still.'"';
							if ($media[0]->med_autoplay) echo ' autoplay="autoplay"';
							echo '></video>';
							echo '<script type="text/javascript">';
							echo "var fmplayer_".str_replace("-","_",$f->field_name)." = new MediaElementPlayer('#mams-article-mediaelement-".$f->field_name."',{features: ['playpause','current','progress','duration','volume','fullscreen','mamsanalytics'";
							if ($config->gapro) echo ",'mamsgoogleanalytics'";
							echo "], enablePluginSmoothing: true, trackId: ".$this->article->track_id.", videoId: ".$media[0]->med_id.", videoExtTitle: '".addslashes($media[0]->med_exttitle)."', videoIntTitle: '".addslashes($media[0]->med_inttitle)."'});";
							echo '</script>';
							if (count($media) > 1) {
								?>
                                <script type="text/javascript">
                                    jQuery(document).ready(function() {
                                        jQuery(".mams-article-media-item").click(function(){
                                            var parent = jQuery(this).parents('.mams-article-media-playlist');
                                            jQuery('.mams-article-media-item',parent).removeClass('selected');
                                            jQuery(this).addClass('selected');
                                        }); <?php
										foreach ($media as $m) {
											echo 'jQuery(document).on("click", ".mampli-'.$m->med_id.'",function(e){';
											echo "fmplayer_".str_replace("-","_",$f->field_name).".pause();";
											echo "fmplayer_".str_replace("-","_",$f->field_name).".setSrc('".$vidprotocol.$config->vid5_url.'/'.$m->med_file."');";
											echo "fmplayer_".str_replace("-","_",$f->field_name).".play();";
											echo "fmplayer_".str_replace("-","_",$f->field_name).".options.videoId = ".$m->med_id.";";
											echo "fmplayer_".str_replace("-","_",$f->field_name).".options.videoExtTitle = '".addslashes($m->med_exttitle)."';";
											echo "fmplayer_".str_replace("-","_",$f->field_name).".options.videoIntTitle = '".addslashes($m->med_inttitle)."';";
											echo '}); ';
										}?>
                                    });
                                </script>
								<?php
							}
							echo '</div>';
						}

						//Audio Player
						if ($media[0]->med_type == 'aud') {
							echo '<div class="mams-article-media-player';
							if (count($media) == 1) echo 'one';
							else if ($config->player_fixed) echo 'fixed';
							echo '">';
							echo '<audio id="mams-article-mediaelement-'.$f->field_name.'" width="'.(int)$config->vid_w.'" ';
							if (!$config->player_fixed) echo 'style="width: 100%;" ';
							echo 'src="'.JURI::base( true ).'/'.$media[0]->med_file.'" type="audio/mp3" controls="controls"></audio>';
							echo '<script type="text/javascript">';
							echo "var fmplayer_".str_replace("-","_",$f->field_name)." = new MediaElementPlayer('#mams-article-mediaelement-".$f->field_name."',{features: ['playpause','current','progress','duration','volume','mamsanalytics'";
							if ($config->gapro) echo ",'mamsgoogleanalytics'";
							echo "], trackId: ".$this->article->track_id.", videoId: ".$media[0]->med_id.", videoExtTitle: '".addslashes($media[0]->med_exttitle)."', videoIntTitle: '".addslashes($media[0]->med_inttitle)."'});";
							echo '</script>';


							if (count($media) > 1) {
								?>
                                <script type="text/javascript">
                                    jQuery(document).ready(function() {
                                        jQuery(".mams-article-media-item").click(function(){
                                            var parent = jQuery(this).parents('.mams-article-media-playlist');
                                            jQuery('.mams-article-media-item',parent).removeClass('selected');
                                            jQuery(this).addClass('selected');
                                        }); <?php
										foreach ($media as $m) {
											echo 'jQuery(document).on("click", ".mampli-'.$m->med_id.'",function(e){';
											echo "fmplayer_".str_replace("-","_",$f->field_name).".pause();fmplayer_".str_replace("-","_",$f->field_name).".setSrc('".JURI::base( true ).'/'.$m->med_file."');fmplayer_".str_replace("-","_",$f->field_name).".play();";
											echo '}); ';
										}?>
                                    });
                                </script>
								<?php
							}
							echo '</div>';
						}
						//Playlist
						if (count($media) > 1) {
							echo '<div class="mams-article-media-playlist';
							if ($config->player_fixed) echo 'fixed';
							echo '">';
							echo '<ul>';
							$first = true;
							foreach ($media as $m) {
								echo '<li class="mams-article-media-item mampli-'.$m->med_id;
								if ($first) { echo ' selected'; $first=false; }
								echo '">';
								echo "";
								echo '<div class="mampli-thumb"><img src="'.$m->med_still.'" border="0" class="size-auto"></div>';
								echo '<div class="mampli-text"><div class="mampli-title">'.$m->med_exttitle.'</div><div class="mampli-desc">'.$m->med_desc;
								echo '</div></div>';
								echo '</li>';
							}
							echo '</ul>';
							echo '</div>';
						}
						echo '</div>';
						echo '<div style="clear:both"></div>';
						echo '</div>';
					}
					break;
                case 'related':
                    if ($this->params->get('show_related',1) && $this->related) {
                        $rlfirst = true;
                        echo '<div class="mams-article-related-links">';
                        foreach ($this->related as $r) {
                            echo '<div class="mams-article-related-link';
                            if ($rlfirst) { echo ' firstlink'; $rlfirst=false; }
                            echo '">';
                            $rartlink = "index.php?option=com_mams&view=article";
	                        if ($r->params->get('article_seclock', 1))  $rartlink .= "&secid=".$r->sec_id.":".$r->sec_alias;
                            $rartlink .= "&artid=".$r->art_id.":".$r->art_alias;
                            if ($r->cats && $r->params->get('article_catlock', 1)) $rartlink .= "&catid=".$r->cats[0]->cat_id;
	                        if ($r->tags && $r->params->get('article_taglock', 1)) $rartlink .= "&tagid=".$r->tags[0]->tag_id;

                            $rartroute=JRoute::_($rartlink);
                            //Thumb
                            if ($r->art_thumb && $this->params->get( 'show_thumb', 1 )) {
                                echo '<div class="mams-article-related-thumb">';
                                echo '<a href="'.$rartroute.'"><img class="mams-article-related-artthumb"';
                                echo ' src="'.$r->art_thumb.'" ';
                                echo ' /></a>';
                                echo '</div>';
                            }
                            echo '<div class="mams-article-related-details">';
                            echo '<div class="mams-article-related-title">';
                            echo '<a href="'.$rartroute.'" class="mams-article-related-artlink">';
                            echo $r->art_title.'</a>';
                            echo '</div>';
                            //Authors
                            if ($r->auts) {
                                echo '<div class="mams-article-related-artaut">';
                                $auts = Array();
                                foreach ($r->auts as $f) {
                                    $auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$f->auth_sec."&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-article-related-autlink">'.$f->auth_fname.(($f->auth_mi) ? " ".$f->auth_mi : "")." ".$f->auth_lname.(($f->auth_titles) ? ", ".$f->auth_titles : "").'</a>';
                                }
                                echo implode(", ",$auts);
                                echo '</div>';
                            }
                            echo '<div class="mams-article-related-pubinfo">';
                            //Section Link
                            echo '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$r->sec_id.":".$r->sec_alias).'" class="mams-article-related-seclink">'.$r->sec_name.'</a>';

                            //Pub Date
                            echo ' published on <strong>';
                            echo date("F j, Y",strtotime($r->art_publish_up));
                            echo '</strong>';

                            //Cat Links
                            if ($r->cats) {
                                echo ' in <em>';
                                $cats = Array();
                                foreach ($r->cats as $c) {
                                    $cats[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=category&catid=".$c->cat_id.":".$c->cat_alias).'" class="mams-article-related-catlink">'.$c->cat_title.'</a>'; //&secid=".$r->sec_id.":".$r->sec_alias."
                                }
                                echo implode(", ",$cats);
                                echo '</em>';
                            }

	                        //Desc
	                        if ($this->params->get('show_related_desc', 1)) {
                                echo '<div class="mams-article-related-artdesc">';
                                echo $r->art_desc;
                                echo '</div>';
	                        }

	                        if ($this->params->get('show_related_readmore', 1)) {
		                        echo '<div class="mams-artlist-artreadmore">';
		                        echo '<a href="' . JRoute::_( $rartlink ) . '" class="mams-artlist-artlink read-more uk-button btn btn-default">';
		                        echo $this->params->get( 'related_readmore_text', "Read More" );
		                        echo '</a>';
		                        echo '</div>';
	                        }

                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                    break;
			}

            // End Field
            echo '</div>';
        }
	}
    // End last group
	if (!$first) { echo '<div style="clear:both"></div>'; echo '</div>'; }
}

echo $this->afterDisplayContent;

//Last Modifed
if ($this->params->get('show_modified',1)) {
	echo '<div class="mams-article-modified">';
	echo 'Last modified: ' . date( "F j, Y", strtotime( $this->article->art_modified ) );
	echo '</div>';
}
if ($this->params->get('divwrapper',1)) { echo '</div>'; }
