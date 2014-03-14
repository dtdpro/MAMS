<?php
defined('_JEXEC') or die();

$app = JFactory::getApplication();
if (count($this->catinfo) == 1) {
	echo '<h2 class="title uk-article-title">';
	echo $this->catinfo[0]->cat_title;
	echo '</h2>';
	if ($this->params->get("show_catimage",0)) {
		echo '<div class="mams-artlist-catimage"><img src="'.$this->catinfo[0]->cat_image.'" class="mams-artlist-catimage-img"></div>';
	}
	echo '<div class="mams-artlist-catdesc">'.$this->catinfo[0]->cat_desc.'</div>';
} else {
	echo '<h2 class="title uk-article-title">';
	echo $this->params->get("page_title",$app->getMenu()->getActive()->title);
	echo '</h2>';
}