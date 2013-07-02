<?php
defined('_JEXEC') or die();

$app = JFactory::getApplication();
if (count($this->secinfo) == 1) {
	echo '<h2 class="title">';
	echo $this->secinfo[0]->sec_name; 
	echo '</h2>';
	echo $this->secinfo[0]->sec_desc;
} else {
	echo '<h2 class="title">';
	echo $this->params->get("page_title",$app->getMenu()->getActive()->title);
	echo '</h2>';
}
