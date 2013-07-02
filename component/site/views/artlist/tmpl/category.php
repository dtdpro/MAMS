<?php
defined('_JEXEC') or die();
echo '<h2 class="title">';
echo $this->catinfo->cat_title; 
echo '</h2>';
if ($this->catinfo->cat_desc) echo $this->catinfo->cat_desc;