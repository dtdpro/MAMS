<?php
defined('_JEXEC') or die();

$app = JFactory::getApplication();
if (count($this->catinfo) == 1) {
	echo '<h2 class="title">';
	echo $this->catinfo[0]->cat_title;
	echo '</h2>';
	echo $this->catinfo[0]->cat_desc;
} else {
	echo '<h2 class="title">';
	echo $this->params->get("page_title",$app->getMenu()->getActive()->title);
	echo '</h2>';
}