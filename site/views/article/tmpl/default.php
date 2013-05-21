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
		if ($this->article->media[0]->med_type == 'vid' || $this->article->media[0]->med_type == 'vids') { //Video Player
			//flash player
			echo '<div id="mediaspace"></div>'."\n";
			echo "<script type='text/javascript'>"."\n";
			echo "jwplayer('mediaspace').setup({"."\n";
	 		if ($this->article->media[0]->med_type == "vid") {
	   			echo "'flashplayer': '".JURI::base( true )."/media/com_mams/vidplyr/player.swf',"."\n";
	 			echo "'file': '".JURI::base( true ).'/'.$this->article->media[0]->med_file."',"."\n";
	 		}
	 		if ($this->article->media[0]->med_type == "vids") {
	 			echo "'modes':[";
	 			echo "{ type: 'flash',\n";
	   			echo "'src': '".JURI::base( true )."/media/com_mams/vidplyr/player.swf',"."\n";
	 			echo "'config':{\n";
	 			if (count($this->article->media) == 1) {
		   			echo "'provider': 'rtmp',"."\n";
		 			echo "'streamer': 'rtmp://".$config->vids_url.'/';
		 			if ($config->vids_app) echo $config->vids_app.'/';
		 			echo "',"."\n";
	 				echo "'file':'mp4:".$this->article->media[0]->med_file."',"."\n";
	 			} else {
	 				echo "'playlist': ["."\n";
	 				foreach ($this->article->media as $m) {
	 					echo "{"."\n";
			   			echo "'provider': 'rtmp',"."\n";
			 			echo "'streamer': 'rtmp://".$config->vids_url.'/';
			 			if ($config->vids_app) echo $config->vids_app.'/';
			 			echo "',"."\n";
	 					echo "'file':'mp4:".$m->med_file."',"."\n";
	 					echo "'image': '".JURI::base( true ).'/'.$m->med_still."',"."\n";
	 					echo "'title': '".JURI::base( true ).$m->med_exttitle."',"."\n";
	 					echo "'description': '".JURI::base( true ).$m->med_desc."',"."\n";
	 					echo "},"."\n";
	 				}
	 				echo "],"."\n";
	 			}
	 			echo "}\n";
	 			echo "},\n";
	 			echo "{ type: 'html5',\n";
	 			echo "'config':{\n";
	 			if (count($this->article->media) == 1) {
	 				echo "'file':'http://".$config->vid5_url."/".$this->article->media[0]->med_file."',"."\n";
	 			} else {
	 				echo "'playlist': ["."\n";
	 				foreach ($this->article->media as $m) {
	 					echo "{"."\n";
	 					echo "'file':'http://".$config->vid5_url."/".$m->med_file."',"."\n";
	 					echo "'image': '".JURI::base( true ).'/'.$m->med_still."',"."\n";
	 					echo "'title': '".JURI::base( true ).$m->med_exttitle."',"."\n";
	 					echo "'description': '".JURI::base( true ).$m->med_desc."',"."\n";
	 					echo "},"."\n";
	 				}
	 				echo "],"."\n";
	 			}
	 			echo "}\n";
	 			echo "}\n";
	 			echo "],\n";
	 		}
			if (count($this->article->media) == 1) echo "'image': '".JURI::base( true ).'/'.$this->article->media[0]->med_still."',"."\n";
			echo "'frontcolor': '000000',"."\n";
			echo "'lightcolor': 'cc9900',"."\n";
			echo "'screencolor': '000000',"."\n";
			echo "'skin': '".JURI::base( true )."/media/com_mams/vidplyr/glow/glow.xml',"."\n";
			echo "'controlbar': 'bottom',"."\n";
			if ($this->article->media[0]->med_autoplay) echo "'autostart':'true',"."\n";
			echo "'width': '".$config->vid_w."',"."\n";
			if (count($this->article->media) == 1) echo "'height': '".((int)$config->vid_h+30)."'";
			else echo "'height': '".((int)$config->vid_h+30+(int)$config->playlist_h)."'";
			if (count($this->article->media) > 1) {
				echo ",'playlist.position': 'bottom',"."\n";
				echo "'playlist.size': '".$config->playlist_h."'";
			}
			echo ",\n'plugins': {'".JURI::base( true )."/media/com_mams/vidplyr/mamstrack.js': {'itemid':".$this->article->media[0]->med_id."}";
			if ($config->gapro)	echo ",'".JURI::base( true )."/media/com_mams/vidplyr/mamsga.js': {}";
			echo "}"."\n";
			echo "});"."\n";
			echo "</script>"."\n";
		}
		if ($this->article->media[0]->med_type == 'aud') { //Audio Player
			echo '<div id="mediaspace"></div>'."\n";
			echo '<script type="text/javascript">'."\n";
			echo "jwplayer('mediaspace').setup({"."\n";
			echo "'width': '".$config->aud_w."',"."\n";
			if (count($this->article->media) == 1) echo "'height': '".((int)$config->aud_h+30)."',"."\n";
			else echo "'height': '".((int)$config->aud_h+30+(int)$config->playlist_h)."',"."\n";
			if (count($this->article->media) == 1) {
				echo "'file': '".JURI::base( true ).'/'.$this->article->media[0]->med_file."',"."\n";
			} else {
 				echo "'playlist': ["."\n";
 				foreach ($this->article->media as $m) {
 					echo "{"."\n";
 					echo "'file':'".JURI::base( true ).'/'.$m->med_file."',"."\n";
 					echo "'image': '".JURI::base( true ).'/'.$m->med_still."',"."\n";
 					echo "'title': '".JURI::base( true ).'/'.$m->med_exttitle."',"."\n";
 					echo "'description': '".JURI::base( true ).'/'.$m->med_desc."',"."\n";
 					echo "},"."\n";
 				}
 				echo "],"."\n";
			}
			if (count($this->article->media) == 1) echo "'image': '".JURI::base( true ).'/'.$this->article->media[0]->med_still."',"."\n";
			echo "'frontcolor': '000000',"."\n";
			echo "'lightcolor': 'cc9900',"."\n";
			echo "'screencolor': '000000',"."\n";
			echo "'skin': '".JURI::base( true )."/media/com_mams/vidplyr/glow/glow.xml',"."\n";
			echo "'controlbar': 'bottom',"."\n";
			if ($this->article->media[0]->med_autoplay) echo "'autostart':'true',"."\n";
			echo "'modes': [{type: 'flash', src: '".JURI::base( true )."/media/com_mams/vidplyr/player.swf'},{type: 'html5'},{type: 'download'}]"."\n";
			echo ",\n'plugins': {'".JURI::base( true )."/media/com_mams/vidplyr/mamstrack.js': {'itemid':".$this->article->media[0]->med_id."}";
			if ($config->gapro)	echo ",'".JURI::base( true )."/media/com_mams/vidplyr/mamsga.js': {}";
			echo "}"."\n";
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

