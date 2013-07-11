<?php

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
echo '<div id="mams-featmod">';
echo '<ul class="mams-featmod-list">';
foreach ($articles as $a) {
	echo '<li class="mams-featmod-listitem">';
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
		echo '<br /><div class="mams-featmod-author">';
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
			echo date("F j, Y",strtotime($a->art_published));
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
	//Readmore
	if ($params->get('show_readmore',0)) {
		echo '<div class="mams-featmod-readmore">';
		echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.":".$a->art_alias).'" class="mams-featmod-artlink read-more">';
		echo $params->get('text_readmore',"Read More");
		echo '</a></div>';
	}
	echo '</li>';
}
echo '</ul>';
echo '</div>';