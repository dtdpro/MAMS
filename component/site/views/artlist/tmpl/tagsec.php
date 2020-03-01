<?php
defined('_JEXEC') or die();
if ($this->params->get('divwrapper',1)) {
	echo '<div id="system" class="'.$this->params->get('wrapperclass','uk-article').'">';
}
$app = JFactory::getApplication();
if (count($this->taginfo) == 1) {
	if ($this->params->get("show_page_heading",1)) {
		echo '<h1 class="title uk-article-title">';
		echo $this->taginfo[0]->tag_title;
		echo '</h1>';
	}
	if ($this->params->get("show_tagimage",0)) {
		echo '<div class="mams-artlist-tagimage"><img src="'.$this->taginfo[0]->tag_image.'" class="mams-artlist-tagimage-img"></div>';
	}
	echo '<div class="mams-artlist-tagdesc">'.$this->taginfo[0]->tag_desc.'</div>';
} else if ($this->params->get("show_page_heading",1)) {
	echo '<h1 class="title uk-article-title">';
	echo $this->params->get("page_title",$app->getMenu()->getActive()->title);
	echo '</h1>';
}
