<?php
defined('_JEXEC') or die();
echo '<h2 class="title">';
if ($this->secinfo) echo $this->secinfo->sec_name.' - ';
echo $this->autinfo->auth_name; 
echo '</h2>';
if ($this->params->get('show_bio',0)) {
	echo '<div class="author-credentials">';
	echo '<strong>'.$this->autinfo->auth_name.'</strong><br />'.$this->autinfo->auth_credentials;
	echo '</div>';
	echo '<div class="author-bio">';
	echo $this->autinfo->auth_bio;
	echo '</div>';
}