<?php
defined('_JEXEC') or die();
if ($this->params->get('divwrapper',1)) {
	echo '<div id="system" class="'.$this->params->get('wrapperclass','uk-article').'">';
}
$cfg = MAMSHelper::getConfig();
//Title
if ($this->params->get("show_page_heading",1)) {
	echo '<h2 class="title uk-article-title">';
	echo $this->author->auth_fname . ( ( $this->author->auth_mi ) ? " " . $this->author->auth_mi : "" ) . " " . $this->author->auth_lname . ( ( $this->author->auth_titles ) ? ", " . $this->author->auth_titles : "" );
	echo '</h2>';
}
echo '<div class="mams-author-credentials">';
//Image
if ($this->author->auth_image) {
	echo '<img class="mams-author-image"';
	echo ' src="'.$this->author->auth_image.'" ';
	echo 'align="left" />';
}
echo $this->author->auth_credentials;
echo '</div>';
echo '<div class="mams-author-bio">';
echo $this->author->auth_bio;
echo '</div>';



//Authored Items
if ($this->published) {
	echo '<div class="mams-author-related">';
	//Aricles
	echo '<div class="mams-author-related-header">Authored Items</div>';
	echo '<div class="mams-author-related-links">';
		foreach ($this->published as $r) {
			$rartlink = "index.php?option=com_mams&view=article&secid=".$r->sec_id.":".$r->sec_alias."&artid=".$r->art_id.":".$r->art_alias;
			if ($r->cats) $rartlink .= "&catid=".$r->cats[0]->cat_id;
			$rartroute = JRoute::_($rartlink);
			echo '<div class="mams-author-related-link">';
			//Thumb
			if ($r->art_thumb && $this->params->get('show_pubed_thumb',1)) {
				echo '<div class="mams-author-related-thumb">';
				echo '<a href="'.$rartroute.'"><img class="mams-author-related-artthumb"';
				echo ' src="'.$r->art_thumb.'" ';
				echo ' /></a>';
				echo '</div>';
			}
			echo '<div class="mams-author-related-details">';
			echo '<div class="mams-author-related-title">';
			echo '<a href="'.$rartroute.'" class="mams-author-artlink">';
			echo $r->art_title.'</a>';
			echo '</div>';
			//Authors
			if ($r->auts) {
				echo '<div class="mams-author-related-artaut">';
					$auts = Array();
					foreach ($r->auts as $f) {
						$auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$f->auth_sec."&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-artlist-autlink">'.$f->auth_fname.(($f->auth_mi) ? " ".$f->auth_mi : "")." ".$f->auth_lname.(($f->auth_titles) ? ", ".$f->auth_titles : "").'</a>';
					}
					echo implode(", ",$auts);
				echo '</div>';
			}
			echo '<div class="mams-author-related-pubinfo">';
			//Section Link
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$r->sec_id.":".$r->sec_alias).'" class="mams-author-seclink">'.$r->sec_name.'</a>';
			
			//Pub Date
			if ($this->params->get('show_pubdate',1)) {
				echo ' published on <strong>';
				echo date("F j, Y",strtotime($r->art_publish_up));
				echo '</strong>';
			}
			
			//Cat Links
			if ($r->cats) {
				echo ' in <em>';
				$cats = Array();
				foreach ($r->cats as $c) {
					$cats[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=category&catid=".$c->cat_id.":".$c->cat_alias).'" class="mams-artlist-catlink">'.$c->cat_title.'</a>'; //&secid=".$r->sec_id.":".$r->sec_alias."
				}
				echo implode(", ",$cats);
				echo '</em>';
			}

			//Desc
			if ($this->params->get('show_pubed_desc', 1)) {
				echo '<div class="mams-author-related-artdesc">';
				echo $r->art_desc;
				echo '</div>';
			}

			if ($this->params->get('show_pubed_readmore', 1)) {
				echo '<div class="mams-author-related-artreadmore">';
				echo '<a href="' . JRoute::_( $rartlink ) . '" class="mams-artlist-artlink read-more uk-button btn btn-default">';
				echo $this->params->get( 'pubed_readmore_text', "Read More" );
				echo '</a>';
				echo '</div>';
			}

			echo '</div>';
			echo '</div>';
			echo '</div>';
		}
	echo '</div>';
	echo '</div>';
}	

//Last Modifed
echo '<div class="mams-author-modified">';
echo 'Last modified: '.date("F j, Y",strtotime($this->author->auth_modified));
echo '</div>';
if ($this->params->get('divwrapper',1)) { echo '</div>'; }
