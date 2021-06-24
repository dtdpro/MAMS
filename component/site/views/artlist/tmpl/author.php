<?php
defined('_JEXEC') or die();
if ($this->params->get('divwrapper',1)) {
	echo '<div id="system" class="'.$this->params->get('wrapperclass','uk-article').'">';
}
$app = JFactory::getApplication();
if (count($this->autinfo) == 1) {
	if ($this->params->get("show_page_heading",1)) {
		echo '<h1 class="title uk-article-title">';
		echo $this->autinfo[0]->auth_fname . ( ( $this->autinfo[0]->auth_mi ) ? " " . $this->autinfo[0]->auth_mi : "" ) . " " . $this->autinfo[0]->auth_lname . ( ( $this->autinfo[0]->auth_titles ) ? ", " . $this->autinfo[0]->auth_titles : "" );
		echo '</h1>';
	}
	if ( $this->headerContent ) {
		echo $this->headerContent;
	}
	if ($this->params->get('show_bio',0)) {
		echo '<div class="mams-author-bio">';
		echo $this->autinfo[0]->auth_bio;
		echo '</div>';
	}
} else if ($this->params->get("show_page_heading",1)) {
	echo '<h1 class="title uk-article-title">';
	echo $this->params->get("page_title",$app->getMenu()->getActive()->title);
	echo '</h1>';
	if ( $this->headerContent ) {
		echo $this->headerContent;
	}
} else {
	if ( $this->headerContent ) {
		echo $this->headerContent;
	}
}

