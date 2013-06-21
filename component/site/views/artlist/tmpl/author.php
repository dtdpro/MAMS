<?php
defined('_JEXEC') or die();
echo '<h2 class="title">';
echo $this->autinfo->auth_name; 
echo '</h2>';
if ($this->params->get('show_bio',0)) {
	echo '<div class="mams-author-credentials">';
	echo '<strong>';
	echo $this->autinfo->auth_fname.(($this->autinfo->auth_mi) ? " ".$this->autinfo->auth_mi : "")." ".$this->autinfo->auth_lname.(($this->autinfo->auth_titles) ? ", ".$this->autinfo->auth_titles : "");
	echo '</strong><br />'.$this->autinfo->auth_credentials;
	echo '</div>';
	echo '<div class="mams-author-bio">';
	echo $this->autinfo->auth_bio;
	echo '</div>';
}