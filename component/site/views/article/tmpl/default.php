<?php
defined('_JEXEC') or die();
$config=MAMSHelper::getConfig();
$user=JFactory::getUser();

$urlnc = $this->getReturnURL();
if ($user->id) {
	$rurl = JRoute::_('index.php?option=com_mams&view=artlist&secid='.$this->article->art_sec);
	$msg = $config->noaccessmsg;
}
else {
	$rurl = JRoute::_('index.php?option=com_users&view=login&return='.$urlnc);
	$msg = $config->loginmsg;
}

//echo '<pre>';
//print_r($this->article);
//echo '</pre>';

//Fields Renderer
if ($this->article->fields) {
	$curgroup = "";
	$first=true;
	foreach ($this->article->fields as $f) {
		if ($f->field_type == "related" && (!$this->params->get('show_related',1) || !$this->related)) { continue; }
		$fn = $f->field_name;
		$gns = "show_".$f->group_name; 
		if ($f->group_name != $curgroup && ($this->article->art_fielddata->$gns == "1" || $f->group_name == "article")) {
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
		echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'">';
		echo '<a name="'.$f->group_name.'-'.$f->field_name.'"></a>';
		if ($f->params->show_title_page && ($this->article->art_fielddata->$gns == "1" || $f->group_name == "article")) {
			echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-title">';
			echo $f->field_title;
			echo '</div>';
		}
		if ($f->field_type != "title" && $f->field_type != "body" && $f->field_type != "pubinfo" && $f->field_type != "related" && ($this->article->art_fielddata->$gns == "1" || $f->group_name == "article")) {
			
			switch ($f->field_type) {
				case "textfield":
				case "textbox":
				case "editor":
					echo $this->article->art_fielddata->$fn;
					break;
				case "auths":
					if ($f->field_name == "art_auths") $auths = $this->article->auts;
					else $auths = $f->data;
					foreach ($auths as $d) {
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-auth mams-article-auth">';
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-auth-name mams-article-auth-name">';
						echo '<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$d->auth_sec."&autid=".$d->auth_id.":".$d->auth_alias).'" class="mams-article-'.$f->group_name.'-'.$f->field_name.'-autlink">'.$d->auth_fname.(($d->auth_mi) ? " ".$d->auth_mi : "")." ".$d->auth_lname.(($d->auth_titles) ? ", ".$d->auth_titles : "").'</a>';
						echo '</div>';
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-auth-cred mams-article-auth-cred">';
						if ($this->params->get('show_authcred',1)) echo $d->auth_credentials;
						echo '</div></div>';
					}
					break;
				case "dloads":
					if ($f->field_name == "art_dloads") $dloads = $this->article->dloads;
					else $dloads = $f->data;
					$firstdl=true;
					foreach ($dloads as $d) {
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-dload">';
						if (in_array($d->access,$user->getAuthorisedViewLevels())) echo '<a href="'.JRoute::_("components/com_mams/dl.php?dlid=".$d->dl_id).'" target="_blank" ';
						else echo '<a href="'.JRoute::_($rurl).'" ';
						echo 'class="mams-article-'.$f->group_name.'-'.$f->field_name.'-artdload mams-article-dllink';
						if ($firstdl) { echo ' firstdload'; $firstdl=false; }
						echo '">';
						echo 'Download '.$d->dl_lname;
						echo '</a>';
						echo '</div>';
					}
					break;
				case "images":
					if ($f->field_name == "art_images") $images = $this->article->images;
					else $images = $f->data;
					$firstimg=true;
					foreach ($images as $i) {
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-image';
						if ($firstimg) { echo ' firstimage'; $firstimg=false; }
						echo '">';
						echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-image-img">';
						if (in_array($i->access,$user->getAuthorisedViewLevels())) echo '<a href="'.$i->img_full.'" target="_blank" ';
						else echo '<a href="'.JRoute::_($rurl).'" ';
						echo 'data-lightbox="group:'.$f->group_name.'-'.$f->field_name.'-images" class="mams-article-'.$f->group_name.'-'.$f->field_name.'-artimage mams-article-imglink';
						echo '">';
						echo '<img src="'.$i->img_thumb.'" class="mams-article-imgthumb">';
						echo '</a></div>';
						if ($this->params->get('show_imgtitle',1) || $this->params->get('show_imgdetails',1)) {
							echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-image-info">';
							if ($this->params->get('show_imgtitle',1)) {
								echo '<div class="mams-article-'.$f->group_name.'-'.$f->field_name.'-image-title">';
								if (in_array($i->access,$user->getAuthorisedViewLevels())) echo '<a href="'.$i->img_full.'" target="_blank" ';
								else echo '<a href="'.JRoute::_($rurl).'" ';
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
				case "links":
					if ($f->field_name == "art_links") $links = $this->article->links;
					else $links = $f->data;
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
					if ($f->field_name == "art_media") $media = $this->article->media;
					else $media = $f->data;
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
						echo 'id="mams-article-mediaelement-'.$f->field_name.'" src="http://'.$config->vid5_url.'/'.$media[0]->med_file.'" type="video/mp4" controls="controls" poster="'.$media[0]->med_still.'"';
						if ($media[0]->med_autoplay) echo ' autoplay="autoplay"';
						echo '></video>';
						echo '<script type="text/javascript">';
						echo "var fmplayer_".str_replace("-","_",$f->field_name)." = new MediaElementPlayer('#mams-article-mediaelement-".$f->field_name."');";
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
									    echo "fmplayer_".str_replace("-","_",$f->field_name).".pause();fmplayer_".str_replace("-","_",$f->field_name).".setSrc('http://".$config->vid5_url.'/'.$m->med_file."');fmplayer_".str_replace("-","_",$f->field_name).".play();";
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
						echo "var fmplayer_".str_replace("-","_",$f->field_name)." = new MediaElementPlayer('#mams-article-mediaelement-".$f->field_name."');";
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
					break;
			} 
		} else if ($f->field_type == "title") {
			//Title
			echo '<h2 class="title">';
			echo $this->article->art_title;
			echo '</h2>';
		} else if ($f->field_type == "body") {
			//Body
			echo '<div class="mams-article-content">';
			echo $this->article->art_content;
			echo '</div>';
		} else if ($f->field_type == "pubinfo" && $this->params->get('show_pubinfo',1)) {
			//Pub Info
			echo '<div class="mams-article-pubinfo">';

			//Section Link
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$this->article->sec_id.":".$this->article->sec_alias).'" class="mams-article-seclink">'.$this->article->sec_name.'</a>';
				
			//Pub Date
			if ($this->params->get('show_pubdate',1)) {
				echo ' published on <strong>';
				echo date("F j, Y",strtotime($this->article->art_publish_up));
				echo '</strong>';
			}
				
			//Cat Links
			if ($this->article->cats) {
				if ($this->params->get('show_pubdate',1)) {
					echo ' in <em>';
				} else {
					echo ' - <em>';
				}
				$cats = Array();
				foreach ($this->article->cats as $c) {
					$cats[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=category&secid=".$this->article->sec_id.":".$this->article->sec_alias."&catid=".$c->cat_id.":".$c->cat_alias).'" class="mams-article-catlink">'.$c->cat_title.'</a>';
				}
				echo implode(", ",$cats);
				echo '</em>';
			}
			echo '</div>';
		} else if ($f->field_type == "related" && $this->params->get('show_related',1) && $this->related) {
			$rlfirst = true;
			echo '<div class="mams-article-related-links">';
			foreach ($this->related as $r) {
				echo '<div class="mams-article-related-link';
				if ($rlfirst) { echo ' firstlink'; $rlfirst=false; }
				echo '">';
				//Thumb
				if ($r->art_thumb) {
					echo '<div class="mams-article-related-thumb">';
					echo '<img class="mams-article-related-artthumb"';
					echo ' src="'.$r->art_thumb.'" ';
					echo ' />';
					echo '</div>';
				}
				echo '<div class="mams-article-related-details">';
				echo '<div class="mams-article-related-title">';
				echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$r->sec_id.":".$r->sec_alias."&artid=".$r->art_id.":".$r->art_alias).'" class="mams-article-related-artlink">';
				echo $r->art_title.'</a>';
				echo '</div>';
				//Authors
				if ($r->auts) {
					echo '<div class="mams-article-related-artaut">';
					$auts = Array();
					foreach ($r->auts as $f) {
						$auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-article-related-autlink">'.$f->auth_fname.(($f->auth_mi) ? " ".$f->auth_mi : "")." ".$f->auth_lname.(($f->auth_titles) ? ", ".$f->auth_titles : "").'</a>';
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
						$cats[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=category&secid=".$r->sec_id.":".$r->sec_alias."&catid=".$c->cat_id.":".$c->cat_alias).'" class="mams-article-related-catlink">'.$c->cat_title.'</a>';
					}
					echo implode(", ",$cats);
					echo '</em>';
				}
				echo '</div>';
				echo '</div>';
				echo '</div>';
			}
			echo '</div>';
		}
		echo '</div>';
	}
	if (!$first) { echo '<div style="clear:both"></div>'; echo '</div>'; }
}

//Last Modifed
echo '<div class="mams-article-modified">';
echo 'Last modified: '.date("F j, Y",strtotime($this->article->art_modified));
echo '</div>';
