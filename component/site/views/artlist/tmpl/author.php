<?php
defined('_JEXEC') or die();

$app = JFactory::getApplication();
if (count($this->autinfo) == 1) {
	echo '<h2 class="title uk-article-title">';
	echo $this->autinfo[0]->auth_fname.(($this->autinfo[0]->auth_mi) ? " ".$this->autinfo[0]->auth_mi : "")." ".$this->autinfo[0]->auth_lname.(($this->autinfo[0]->auth_titles) ? ", ".$this->autinfo[0]->auth_titles : "");
	echo '</h2>';
	if ($this->params->get('show_bio',0)) {
		echo '<div class="mams-author-bio">';
		echo $this->autinfo[0]->auth_bio;
		echo '</div>';
	}
} else {
	echo '<h2 class="title uk-article-title">';
	echo $this->params->get("page_title",$app->getMenu()->getActive()->title);
	echo '</h2>';
}
