<?php
defined('_JEXEC') or die();
echo '<h2 class="title">';
if ($this->secinfo) echo $this->secinfo->sec_name.' - ';
echo $this->catinfo->cat_title; 
echo '</h2>';