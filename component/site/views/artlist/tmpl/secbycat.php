<?php
defined('_JEXEC') or die();

$app = JFactory::getApplication();
if (count($this->secinfo) == 1) {
	echo '<h2 class="title">';
	echo $this->secinfo[0]->sec_name; 
	echo '</h2>';
	if ($this->params->get("show_secimage",0)) {
		echo '<div class="mams-artlist-secimage"><img src="'.$this->secinfo[0]->sec_image.'" class="mams-artlist-secimage-img"></div>';
	}
	echo '<div class="mams-secbycat-header">';
	if ($this->secinfo[0]->sec_desc) echo '<div class="mams-secbycat-header-secdesc">'.$this->secinfo[0]->sec_desc.'</div>';
	echo '<div class="mams-secbycat-header-catlist">';
	echo '<ul>';
	foreach ($this->cats as $c) {
		echo '<li><a href="#'.$c->cat_alias.'">'.$c->cat_title.'</a></li>';
	}
	echo '</ul>';
	echo '</div>';
	echo '</div>';
} else {
	echo '<h2 class="title">';
	echo $this->params->get("page_title",$app->getMenu()->getActive()->title);
	echo '</h2>';
	echo '<div class="mams-secbycat-header">';
	echo '<div class="mams-secbycat-header-catlist">';
	echo '<ul>';
	foreach ($this->cats as $c) {
		echo '<li><a href="#'.$c->cat_alias.'">'.$c->cat_title.'</a></li>';
	}
	echo '</ul>';
	echo '</div>';
	echo '</div>';
}
echo '<div class="mams-secbycat-cats">';
foreach ($this->cats as $c) {
	echo '<div class="mams-secbycat-cat">';
	echo '<div class="mams-secbycat-cat-header"><a name="'.$c->cat_alias.'"></a>'.$c->cat_title.'<a class="tm-totop-scroller" data-uk-smooth-scroll href="#"></a></div>';
	echo '<div class="mams-secbycat-cat-items">'; 
	$this->articles = $c->articles;
	include 'artlist.php';
	echo '</div></div>';
}
echo '</div>';
