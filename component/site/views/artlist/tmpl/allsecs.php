<?php
defined('_JEXEC') or die();

$app = JFactory::getApplication();
echo '<h2 class="title">';
echo $this->params->get("page_title",$app->getMenu()->getActive()->title);
echo '</h2>';
