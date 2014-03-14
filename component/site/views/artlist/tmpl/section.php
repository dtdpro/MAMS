<?php
defined('_JEXEC') or die();

$app = JFactory::getApplication();
if (count($this->secinfo) == 1) {
	echo '<h2 class="title uk-article-title">';
	echo $this->secinfo[0]->sec_name;
	echo '</h2>';
	if ($this->params->get("show_secimage",0)) {
		echo '<div class="mams-artlist-secimage"><img src="'.$this->secinfo[0]->sec_image.'" class="mams-artlist-secimage-img"></div>';
	}
	echo '<div class="mams-artlist-secdesc">'.$this->secinfo[0]->sec_desc.'</div>';
} else {
	echo '<h2 class="title uk-article-title">';
	echo $this->params->get("page_title",$app->getMenu()->getActive()->title);
	echo '</h2>';
}
