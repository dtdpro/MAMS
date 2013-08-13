<?php
defined('_JEXEC') or die();
$cfg = MAMSHelper::getConfig();
//Title
echo '<h2 class="title">';
echo $this->author->auth_name; 
echo '</h2>';
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

//Last Modifed
echo '<div class="mams-author-modified">';
echo 'Last modified: '.date("F j, Y",strtotime($this->author->auth_modified));
echo '</div>';


//Related Items
if ($this->published) {
	echo '<div class="mams-author-related">';
	//Aricles
	echo '<div class="mams-author-related-title">Authored Items</div>';
	echo '<div class="mams-author-related-links">';
		foreach ($this->published as $r) {
			echo '<div class="mams-author-related-link">';
			//Thumb
			if ($r->art_thumb) {
				echo '<img class="mams-author-related-artthumb"';
				echo ' src="'.$r->art_thumb.'" ';
				echo 'align="left" width="70" />';
			}
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$r->sec_id.":".$r->sec_alias."&artid=".$r->art_id.":".$r->art_alias).'" class="mams-author-artlink">';
			echo $r->art_title.'</a>';
			//Authors
			if ($r->auts) {
				echo '<div class="mams-author-related-artaut">';
					$auts = Array();
					foreach ($r->auts as $f) {
						$auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$f->auth_sec."&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-artlist-autlink">'.$f->auth_name.'</a>';
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
				echo date("F j, Y",strtotime($r->art_published));
				echo '</strong>';
			}
			
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
//Courses
if ($cfg->continued && $this->courses) {
	echo '<div class="mams-author-related">';
	echo '<div class="mams-author-related-title">CE Courses</div>';
	echo '<div class="mams-author-related-links">';
	foreach ($this->courses as $r) {
		echo '<div class="mams-author-related-link">';
		//Thumb
		if ($r->art_thumb) {
			echo '<img class="mams-author-related-artthumb"';
			echo ' src="'.$r->course_previmg.'" ';
			echo 'align="left" width="70" />';
		}
		echo '<a href="'.JRoute::_("index.php?option=com_continued&view=course&course=".$r->course_id).'" class="mams-author-artlink">';
		echo $r->course_name.'</a>';
		//Authors
		if ($r->auts) {
			echo '<div class="mams-author-related-artaut">';
			$auts = Array();
			foreach ($r->auts as $f) {
				$auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=author&secid=".$r->sec_id.":".$r->sec_alias."&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-artlist-autlink">'.$f->auth_name.'</a>';
			}
			echo implode(", ",$auts);
			echo '</div>';
		}
		echo '<div class="mams-author-related-pubinfo">';
		//Course Cat Link
		echo '<a href="'.JRoute::_("index.php?option=com_continued&view=continued&cat=".$r->cat_id).'" class="mams-author-seclink">'.$r->cat_name.'</a>';
			
		//Pub Date
		echo '<br />Released:  <strong>';
		echo date("F j, Y",strtotime($r->course_startdate));
		echo '</strong> Expires:  <strong>';
		echo date("F j, Y",strtotime($r->course_enddate));
		echo '</strong>';
		/*	
		//Cat Links
		if ($r->cats) {
			echo ' in <em>';
			$cats = Array();
			foreach ($r->cats as $c) {
				$cats[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=category&secid=".$r->sec_id.":".$r->sec_alias."&catid=".$c->cat_id.":".$c->cat_alias).'" class="mams-artlist-catlink">'.$c->cat_title.'</a>';
			}
			echo implode(", ",$cats);
			echo '</em>';
		}*/
		echo '</div>';
		echo '</div>';
	}
	echo '</div>';
	echo '</div>';
}


