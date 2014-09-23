<?php
defined('_JEXEC') or die();
if ($this->params->get('divwrapper',1)) {
	echo '<div id="system" class="'.$this->params->get('wrapperclass','uk-article').'">';
}
$app = JFactory::getApplication();
echo '<h2 class="title uk-article-title">';
echo $this->params->get("page_title",$app->getMenu()->getActive()->title);
echo '</h2>';
