<?php
defined('_JEXEC') or die();
if ($this->params->get('divwrapper',1)) {
	echo '<div id="system" class="'.$this->params->get('wrapperclass','uk-article').'">';
}
$app = JFactory::getApplication();
if (count($this->secinfo) == 1) {
	if ($this->params->get("show_page_heading",1)) {
		echo '<h1 class="title uk-article-title">';
		echo $this->secinfo[0]->sec_name;
		echo '</h1>';
	}
	if ( $this->headerContent ) {
		echo $this->headerContent;
	}
	if ($this->params->get("show_secimage",0)) {
		echo '<div class="mams-artlist-secimage"><img src="'.$this->secinfo[0]->sec_image.'" class="mams-artlist-secimage-img"></div>';
	}
	echo '<div class="mams-artlist-secdesc">'.$this->secinfo[0]->sec_content.'</div>';
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


