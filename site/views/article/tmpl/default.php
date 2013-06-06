<?php
defined('_JEXEC') or die();
$config=MAMSHelper::getConfig();
//Title
echo '<h2 class="title">';
echo $this->article->art_title; 
echo '</h2>';

if ($this->params->get('show_pubinfo',1)) {
	//Pub Info
	echo '<div class="mams-article-pubinfo">';
	
		//Section Link
		echo '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$this->article->sec_id.":".$this->article->sec_alias).'" class="mams-article-seclink">'.$this->article->sec_name.'</a>';
		
		//Pub Date
		if ($this->params->get('show_pubdate',1)) {
			echo ' published on <strong>';
			echo date("F j, Y",strtotime($this->article->art_published));
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
}

//Media
if ($this->article->media) {
	echo '<div class="mams-article-media">';
		echo '<div align="center">';
		
		echo '<div class="mams-article-mediawrap"';
		if ($config->player_fixed) echo ' style="width: '.(int)$config->vid_w.'px;"';
		echo '>';
		if ($this->article->media[0]->med_type == 'vid' || $this->article->media[0]->med_type == 'vids') { //Video Player
			echo '<div class="mams-article-media-player';
			if (count($this->article->media) == 1) echo 'one';
			else if ($config->player_fixed) echo 'fixed';
			echo '">';
			echo '<video width="'.(int)$config->vid_w.'" height="'.(int)$config->vid_h.'" ';
			if (!$config->player_fixed) echo 'style="width: 100%; height: 100%;" ';
			echo 'id="mams-article-mediaelement" src="http://'.$config->vid5_url.'/'.$this->article->media[0]->med_file.'" type="video/mp4" controls="controls" poster="'.$this->article->media[0]->med_still.'"';
			if ($this->article->media[0]->med_autoplay) echo ' autoplay="autoplay"';
			echo '></video>';
			echo '<script type="text/javascript">';
			echo "var fmplayer = new MediaElementPlayer('#mams-article-mediaelement');";
			echo '</script>';
			if (count($this->article->media) > 1) {
				?>
					<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery(".mams-article-media-item").click(function(){	
							var parent = jQuery(this).parents('.mams-article-media-playlist');	
							jQuery('.mams-article-media-item',parent).removeClass('selected');
							jQuery(this).addClass('selected');
						}); <?php 
						foreach ($this->article->media as $m) {
							echo 'jQuery(document).on("click", ".mampli-'.$m->med_id.'",function(e){';
						    echo "fmplayer.pause();fmplayer.setSrc('http://".$config->vid5_url.'/'.$m->med_file."');fmplayer.play();";
							echo '}); ';
						}?>
					});
					</script>
				<?php 
			}
			echo '</div>';
			if (count($this->article->media) > 1) {
				echo '<div class="mams-article-media-playlist';
				if ($config->player_fixed) echo 'fixed';
				echo '">';
				echo '<ul>';
				$first = true;
				foreach ($this->article->media as $m) {
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
		}
		
		if ($this->article->media[0]->med_type == 'aud') { //Audio Player
			echo '<div class="mams-article-media-player';
			if (count($this->article->media) == 1) echo 'one';
			else if ($config->player_fixed) echo 'fixed';
			echo '">';
			echo '<audio id="mams-article-mediaelement" width="'.(int)$config->vid_w.'" ';
			if (!$config->player_fixed) echo 'style="width: 100%;" ';
			echo 'src="'.JURI::base( true ).'/'.$this->article->media[0]->med_file.'" type="audio/mp3" controls="controls"></audio>';
			echo '<script type="text/javascript">';
			echo "var fmplayer = new MediaElementPlayer('#mams-article-mediaelement');";
			echo '</script>';
			if (count($this->article->media) > 1) {
				?>
					<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery(".mams-article-media-item").click(function(){	
							var parent = jQuery(this).parents('.mams-article-media-playlist');	
							jQuery('.mams-article-media-item',parent).removeClass('selected');
							jQuery(this).addClass('selected');
						}); <?php 
						foreach ($this->article->media as $m) {
							echo 'jQuery(document).on("click", ".mampli-'.$m->med_id.'",function(e){';
						    echo "fmplayer.pause();fmplayer.setSrc('".JURI::base( true ).'/'.$m->med_file."');fmplayer.play();";
							echo '}); ';
						}?>
					});
					</script>
				<?php 
			}
			echo '</div>';
			if (count($this->article->media) > 1) {
				echo '<div class="mams-article-media-playlist';
				if ($config->player_fixed) echo 'fixed';
				echo '">';
				echo '<ul>';
				$first = true;
				foreach ($this->article->media as $m) {
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
		}
		echo '</div>';
		echo '<div style="clear:both"></div>';
		echo '</div>';
	echo '</div>';
}

//Downloads
if ($this->article->dloads) {
	echo '<div class="mams-article-downloads">';
		$dloads = Array();
		foreach ($this->article->dloads as $d) {
			$dloads[]='<a href="'.JRoute::_("components/com_mams/dl.php?dlid=".$d->dl_id).'" class="mams-article-dllink" target="_blank">Download '.$d->dl_lname.'</a>';
		}
		echo implode(" ",$dloads);
	echo '</div>';
}

//Authors
if ($this->article->auts) {
	echo '<div class="mams-article-auths">';
		foreach ($this->article->auts as $f) {
			echo '<div class="mams-article-auth">';
			echo '<strong><a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$f->auth_sec."&autid=".$f->auth_id.":".$f->auth_alias).'" ';
			echo 'class="mams-article-autlink">';
			echo $f->auth_name;
			echo '</a></strong><br />'.$f->auth_credentials;
			echo '</div>';
		}
	echo '</div>';
}

//Article Title
if ($this->params->get('show_title2',0)) {
	echo '<div class="mams-article-title">';
	echo $this->article->art_title;
	echo '</div>';
}

//Article Body
echo '<div class="mams-article-content">';
echo $this->article->art_content;
echo '</div>';

//Links
if ($this->article->links) {
	echo '<div class="mams-article-links">';
	foreach ($this->article->links as $f) {
		echo '<div class="mams-article-link">';
		echo '<strong><a href="'.$f->link_url.'" ';
		echo 'target="'.$f->link_target.'" ';
		echo 'class="mams-article-artlink">';
		echo $f->link_title;
		echo '</a>';
		echo '</div>';
	}
	echo '</div>';
}

//Last Modifed
echo '<div class="mams-article-modified">';
echo 'Last modified: '.date("F j, Y",strtotime($this->article->art_modified));
echo '</div>';


//Related Items
if ($this->relatedbycat) {
echo '<div class="mams-article-related">';
	echo '<div class="mams-article-related-title">Related Items by Category</div>';
	echo '<div class="mams-article-related-links">';
		foreach ($this->relatedbycat as $r) {
			echo '<div class="mams-article-related-link">';
			//Thumb
			if ($r->art_thumb) {
				echo '<img class="mams-article-related-artthumb"';
				echo ' src="'.$r->art_thumb.'" ';
				echo 'align="left" />';
			}
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$r->sec_id.":".$r->sec_alias."&artid=".$r->art_id.":".$r->art_alias).'" class="mams-article-artlink">';
			echo $r->art_title.'</a>';
			//Authors
			if ($r->auts) {
				echo '<div class="mams-article-related-artaut">';
					$auts = Array();
					foreach ($r->auts as $f) {
						$auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-artlist-autlink">'.$f->auth_name.'</a>';
					}
					echo implode(", ",$auts);
				echo '</div>';
			}
			echo '<div class="mams-article-related-pubinfo">';
			//Section Link
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$r->sec_id.":".$r->sec_alias).'" class="mams-article-seclink">'.$r->sec_name.'</a>';
			
			//Pub Date
			echo ' published on <strong>';
			echo date("F j, Y",strtotime($r->art_published));
			echo '</strong>';
			
			//Cat Links
			if ($r->cats) {
				echo ' in <em>';
				$cats = Array();
				foreach ($r->cats as $c) {
					$cats[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=category&secid=".$r->sec_id.":".$r->sec_alias."&catid=".$c->cat_id.":".$c->cat_alias).'" class="mams-artlist-catlink">'.$c->cat_title.'</a>';
				}
				echo implode(", ",$cats);
				echo '</em>';
			}
			echo '</div>';
			echo '</div>';
		}
	echo '</div>';
echo '</div>';
}

if ($this->relatedbyaut) {
echo '<div class="mams-article-related">';
	echo '<div class="mams-article-related-title">Related Items by Author</div>';
	echo '<div class="mams-article-related-links">';
		foreach ($this->relatedbyaut as $r) {
			echo '<div class="mams-article-related-link">';
			//Thumb
			if ($r->art_thumb) {
				echo '<img class="mams-article-related-artthumb"';
				echo ' src="'.$r->art_thumb.'" ';
				echo 'align="left" />';
			}
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$r->sec_id.":".$r->sec_alias."&artid=".$r->art_id.":".$r->art_alias).'" class="mams-article-artlink">';
			echo $r->art_title.'</a>';
			//Authors
			if ($r->auts) {
				echo '<div class="mams-article-related-artaut">';
					$auts = Array();
					foreach ($r->auts as $f) {
						$auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-artlist-autlink">'.$f->auth_name.'</a>';
					}
					echo implode(", ",$auts);
				echo '</div>';
			}
			echo '<div class="mams-article-related-pubinfo">';
			//Section Link
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$r->sec_id.":".$r->sec_alias).'" class="mams-article-seclink">'.$r->sec_name.'</a>';
			
			//Pub Date
			echo ' published on <strong>';
			echo date("F j, Y",strtotime($r->art_published));
			echo '</strong>';
			
			//Cat Links
			if ($r->cats) {
				echo ' in <em>';
				$cats = Array();
				foreach ($r->cats as $c) {
					$cats[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=category&secid=".$r->sec_id.":".$r->sec_alias."&catid=".$c->cat_id.":".$c->cat_alias).'" class="mams-artlist-catlink">'.$c->cat_title.'</a>';
				}
				echo implode(", ",$cats);
				echo '</em>';
			}
			echo '</div>';
			echo '</div>';
		}
	echo '</div>';
echo '</div>';
}

