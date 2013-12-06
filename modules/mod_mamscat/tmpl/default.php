<?php

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
echo '<div class="mams-featmod">';
if ($params->get('show_image',0)) {
	echo '<div class="mams-featmod-image">';
	echo '<img src="'.$catinfo->cat_image.'" class="mams-featmod-image-img">';
	echo '</div>';
}
$firstart=true;
foreach ($articles as $a) {
	echo '<div class="mams-featmod-article';
	if ($firstart) { echo ' first-child'; $firstart=false; }
	echo '">';
	if ($a->art_thumb && $params->get('show_thumb',0)) {
		echo '<div class="mams-featmod-thumb">';
		echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.":".$a->art_alias).'">';
		echo '<img border="0" class="mams-featmod-artthumb" src="'.$a->art_thumb.'" /></a>';
		echo '</div>';
	}
	if ($a->art_thumb && $params->get('show_thumb',0)) echo '<div class="mams-featmod-articleinfowt">';
	else echo '<div class="mams-featmod-articleinfo">';
	echo '<div class="mams-featmod-title">';
	echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.":".$a->art_alias).'">';
	echo $a->art_title;
	echo '</a></div>';
	if ($a->auts && $params->get('show_author',1)) {
		$auts = Array();
		foreach ($a->auts as $f) {
			if ($params->get('link_pubinfo',0)) $auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$f->auth_sec."&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-artlist-autlink">'.$f->auth_fname.(($f->auth_mi) ? " ".$f->auth_mi : "")." ".$f->auth_lname.(($f->auth_titles) ? ", ".$f->auth_titles : "").'</a>';
			else $auts[]=$f->auth_fname.(($f->auth_mi) ? " ".$f->auth_mi : "")." ".$f->auth_lname.(($f->auth_titles) ? ", ".$f->auth_titles : "");
		}
		echo '<div class="mams-featmod-author">';
		echo implode(", ",$auts);
		echo '</div>';
	}
	if ($params->get('show_pubinfo',1)) {
		//Section Link
		echo '<div class="mams-featmod-pubinfo">';
		if ($params->get('show_pubsec',1)) {
			
			echo '<span class="mams-featmod-sec">';
			if ($params->get('link_pubinfo',0)) echo '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$a->sec_id.":".$a->sec_alias).'" class="mams-artlist-seclink">';
			echo '<em>'.$a->sec_name.'</em>';
			if ($params->get('link_pubinfo',0)) echo '</a>';
			echo '</span>';
		}
		
		//Pub Date
		if ($params->get('show_pubdate',1)) {
			echo '<span class="mams-featmod-pubdate">';
			echo ' published on <strong>';
			echo date("F j, Y",strtotime($a->art_publish_up));
			echo '</strong>';
			echo '</span>';
		}
		
		//Cat Links
		if ($a->cats && $params->get('show_pubcat',1)) {
			echo '<span class="mams-featmod-cat">';
			if ($params->get('show_pubdate',1)) {
				echo ' in <em>';
			} else {
				echo ' - <em>';
			}
			$cats = Array();
			foreach ($a->cats as $c) {
				if ($params->get('link_pubinfo',0)) $cats[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=category&secid=".$a->sec_id.":".$a->sec_alias."&catid=".$c->cat_id.":".$c->cat_alias).'" class="mams-artlist-catlink">'.$c->cat_title.'</a>';
				else $cats[]=$c->cat_title;
			}
			echo implode(", ",$cats);
			echo '</em>';
			echo '</span>';
		}
		echo '</div>';
	}
		
	//Description
	if ($params->get('show_desc',0)) {
		echo '<div class="mams-featmod-desc">';
		echo $a->art_desc;
		echo '</div>';
	}
	
	//Additional Fields
	if ($params->get('show_allfields',0)) {
		if ($a->fields) {
			$curgroup = "";
			$first=true;
			foreach ($a->fields as $f) {
				$fn = $f->field_name;
				if ($a->art_fielddata->$fn || $f->data) {
					if ($f->group_name != $curgroup) {
						if (!$first) { echo '</div>';  }
						else { $first=false; }
						echo '<div class="mams-featmod-'.$f->group_name.'">';
						$curgroup = $f->group_name;
						if ($f->group_show_title) {
							echo '<div class="mams-featmod-'.$f->group_name.'-title">';
							if ($f->group_params->linktitlemod) echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.':'.$a->art_alias).'#'.$f->group_name.'">';
							echo $f->group_title;
							if ($f->group_params->linktitlemod) echo '</a>';
							echo '</div>';
						}
					}
					if ($f->field_params->show_title_desc) {
						echo '<div class="mams-featmod-'.$f->group_name.'-'.$f->field_name.'-title">';
						echo $f->field_title;
						echo '</div>';
					}
					echo '<div class="mams-featmod-'.$f->group_name.'-'.$f->field_name.'">';
					if ($f->field_params->pretext) echo $f->field_params->pretext.' ';
					switch ($f->field_type) {
						case "textfield":
							if ($f->field_params->linktext==1) echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.':'.$a->art_alias).'#'.$f->field_name.'">';
							if ($f->field_params->linktext==2) echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.':'.$a->art_alias).'#'.$f->group_name.'">';
							echo $a->art_fielddata->$fn;
							if ($f->field_params->linktext) echo '</a>';
							break;
						case "textbox":
						case "editor":
							echo $a->art_fielddata->$fn;
							break;
						case "auths":
							$auts = Array();
							foreach ($f->data as $d) {
								if ($params->get('link_pubinfo',0)) { $auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$d->auth_sec."&autid=".$d->auth_id.":".$d->auth_alias).'" class="mams-featmod-'.$f->group_name.'-'.$f->field_name.'-autlink">'.$d->auth_fname.(($d->auth_mi) ? " ".$d->auth_mi : "")." ".$d->auth_lname.(($d->auth_titles) ? ", ".$d->auth_titles : "").'</a>'; }
								else { $auts[] = $d->auth_fname.(($d->auth_mi) ? " ".$d->auth_mi : "")." ".$d->auth_lname.(($d->auth_titles) ? ", ".$d->auth_titles : ""); }
							}
							echo implode(", ",$auts);
							break;
						case "dloads":
							$firstdl=true;
							foreach ($f->data as $d) {
								echo '<div class="mams-featmod-'.$f->group_name.'-'.$f->field_name.'-dload">';
								echo '<a href="'.JRoute::_("components/com_mams/dl.php?dlid=".$d->dl_id).'" ';
								echo 'target="_blank" ';
								echo 'class="mams-featmod-'.$f->group_name.'-'.$f->field_name.'-artdload';
								if ($firstdl) { echo ' firstdload'; $firstdl=false; }
								echo '">';
								echo 'Download '.$d->dl_lname;
								echo '</a>';
								echo '</div>';
							}
							break;
						case "links":
							$firstlink=true;
							foreach ($f->data as $d) {
								echo '<div class="mams-featmod-'.$f->group_name.'-'.$f->field_name.'-link">';
								echo '<a href="'.$d->link_url.'" ';
								echo 'target="'.$d->link_target.'" ';
								echo 'class="mams-featmod-'.$f->group_name.'-'.$f->field_name.'-artlink';
								if ($firstlink) { echo ' firstlink'; $firstlink=false; }
								echo '">';
								echo $d->link_title;
								echo '</a>';
								echo '</div>';
							}
							break;
					}
					if ($f->field_params->posttext) echo ' '.$f->field_params->posttext;
					echo '</div>';
				}
			}
			if (!$first) echo '</div>';
		}
	}
	
	//Readmore
	if ($params->get('show_readmore',0)) {
		echo '<div class="mams-featmod-readmore">';
		echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.":".$a->art_alias).'" class="mams-featmod-artlink read-more">';
		echo $params->get('text_readmore',"Read More");
		echo '</a></div>';
	}
	echo '</div>';
	echo '</div>';
}
echo '</div>';