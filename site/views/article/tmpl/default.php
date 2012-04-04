<?php
defined('_JEXEC') or die();
$config=MAMSHelper::getConfig();
//Title
echo '<h2 class="title">';
echo $this->article->art_title; 
echo '</h2>';

//Pub Info
echo '<div class="mams-article-pubinfo">';

	//Section Link
	echo '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$this->article->sec_id.":".$this->article->sec_alias).'" class="mams-article-seclink">'.$this->article->sec_name.'</a>';
	
	//Pub Date
	echo ' published on <strong>';
	echo date("F j, Y",strtotime($this->article->art_published));
	echo '</strong>';
	
	//Cat Links
	if ($this->article->cats) {
		echo ' in <em>';
		$cats = Array();
		foreach ($this->article->cats as $c) {
			$cats[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=category&secid=".$this->article->sec_id.":".$this->article->sec_alias."&catid=".$c->cat_id.":".$c->cat_alias).'" class="mams-article-catlink">'.$c->cat_title.'</a>';
		}
		echo implode(", ",$cats);
		echo '</em>';
	}
	
echo '</div>';

//Media
if ($this->article->media) {
	echo '<div class="mams-article-media">';
		echo '<div align="center">';
		$detect_iDevice = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone") || strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
		if ($detect_iDevice) {
			//html 5 video, only for iDevices
			if ($this->article->media[0]->med_type == "vid") echo '<video src="'.JURI::base( true ).'/'.$this->article->media[0]->med_file.'" poster="'.JURI::base( true ).'/'.$this->article->media[0]->med_still.'" width="'.$config->vid_w.'" height="'.$config->vid_h.'" controls preload></video>';
	 		if ($this->article->media[0]->med_type == "vids") echo '<video src="http://'.$config->vids_url.':1935/'.$config->vids_app.'/'.'/mp4:'.urlencode($this->article->media[0]->med_file).'/playlist.m3u8" poster="'.JURI::base( true ).'/'.$this->article->media[0]->med_still.'" width="'.$config->vid_w.'" height="'.$config->vid_h.'" controls preload></video>';
		} else {
			//flash player
			echo '<div id="mediaspace"></div>'."\n";
			echo "<script type='text/javascript'>"."\n";
			echo "jwplayer('mediaspace').setup({"."\n";
	   		echo "'flashplayer': '".JURI::base( true )."/media/com_mams/vidplyr/player.swf',"."\n";
	 		if ($this->article->media[0]->med_type == "vid") echo "'file': '".JURI::base( true ).'/'.$this->article->media[0]->med_file."',"."\n";
	 		if ($this->article->media[0]->med_type == "vids") {
	 			echo "'provider': 'rtmp',"."\n";
	 			echo "'streamer': 'rtmp://".$config->vids_url.'/'.$config->vids_app.'/'."',"."\n";
	 			echo "'file':'mp4:".$this->article->media[0]->med_file."',"."\n";
	 		}
			echo "'image': '".JURI::base( true ).'/'.$this->article->media[0]->med_still."',"."\n";
			echo "'frontcolor': '000000',"."\n";
			echo "'lightcolor': 'cc9900',"."\n";
			echo "'screencolor': '000000',"."\n";
			echo "'skin': '".JURI::base( true )."/media/com_mams/vidplyr/glow.zip',"."\n";
			echo "'controlbar': 'bottom',"."\n";
			echo "'width': '".$config->vid_w."',"."\n";
			echo "'height': '".((int)$config->vid_h+30)."'";
			if ($config->gapro)	echo ",\n'plugins': {'gapro-2': {}}"."\n";
			echo "});"."\n";
			echo "</script>"."\n";
		}
		echo '</div>';
	echo '</div>';
}

//Downloads
if ($this->article->dloads) {
	echo '<div class="mams-article-downloads">';
		$dloads = Array();
		foreach ($this->article->dloads as $d) {
			$dloads[]='<a href="'.JRoute::_("/components/com_mams/dl.php?dlid=".$d->dl_id).'" class="mams-article-dllink" target="_blank">Download '.$d->dl_lname.'</a>';
		}
		echo implode(" ",$dloads);
	echo '</div>';
}

//Authors
if ($this->article->auts) {
	echo '<div class="mams-article-auths">';
		foreach ($this->article->auts as $f) {
			echo '<div class="mams-article-auth">';
			echo '<strong><a href="'.JRoute::_("index.php?option=com_mams&view=author&autid=".$f->auth_id.":".$f->auth_alias).'" ';
			echo 'class="mams-article-autlink">';
			echo $f->auth_name;
			echo '</a></strong><br />'.$f->auth_credentials;
			echo '</div>';
		}
	echo '</div>';
}

//Article Title
echo '<div class="mams-article-title">';
echo $this->article->art_title;
echo '</div>';

//Article Body
echo '<div class="mams-article-content">';
echo $this->article->art_content;
echo '</div>';

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
				echo 'align="left" width="70" />';
			}
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$r->sec_id.":".$r->sec_alias."&artid=".$r->art_id.":".$r->art_alias).'" class="mams-article-artlink">';
			echo $r->art_title.'</a>';
			//Authors
			if ($r->auts) {
				echo '<div class="mams-article-related-artaut">';
					$auts = Array();
					foreach ($r->auts as $f) {
						$auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=author&secid=".$r->sec_id.":".$r->sec_alias."&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-artlist-autlink">'.$f->auth_name.'</a>';
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
				echo 'align="left" width="70" />';
			}
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$r->sec_id.":".$r->sec_alias."&artid=".$r->art_id.":".$r->art_alias).'" class="mams-article-artlink">';
			echo $r->art_title.'</a>';
			//Authors
			if ($r->auts) {
				echo '<div class="mams-article-related-artaut">';
					$auts = Array();
					foreach ($r->auts as $f) {
						$auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=author&secid=".$r->sec_id.":".$r->sec_alias."&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-artlist-autlink">'.$f->auth_name.'</a>';
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

