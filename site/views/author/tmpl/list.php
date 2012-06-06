<?php 
defined('_JEXEC') or die();
echo '<h2 class="title">';
echo $this->params->get("page_title",'Authors'); 
echo '</h2>';

//Authors
if ($this->autlist) {
	echo '<div class="mams-author-auths">';
	foreach ($this->autlist as $s) {
		if ($s->authors) {
			echo '<div class="mams-author-authsec">';
			echo '<div class="mams-author-authsec-title">'.$s->sec_name.'</div>';
			echo '<div class="mams-author-authsec-auths">';
			foreach ($s->authors as $f) {
				echo '<div class="mams-author-authname">';
				echo '<a href="'.JRoute::_("index.php?option=com_mams&view=author&autid=".$f->auth_id.":".$f->auth_alias).'" ';
				echo 'class="mams-article-autlink">';
				echo $f->auth_name;
				echo '</a>';
				echo '</div>';
			}
			echo '</div>';
			echo '</div>';
		}
	}
	echo '</div>';
}