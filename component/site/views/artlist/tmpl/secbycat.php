<?php
defined('_JEXEC') or die();
if ($this->params->get('divwrapper',1)) {
	echo '<div id="system" class="'.$this->params->get('wrapperclass','uk-article').'">';
}
$first=true;
$app = JFactory::getApplication();
if (count($this->secinfo) == 1) {
	if ($this->params->get("show_page_heading",1)) {
		echo '<h1 class="title uk-article-title">';
		echo $this->secinfo[0]->sec_name;
		echo '</h1>';
	}
	if ($this->headerContent) {
		echo $this->headerContent;
	}
	if ($this->params->get("show_secimage",0)) {
		echo '<div class="mams-artlist-secimage"><img src="'.$this->secinfo[0]->sec_image.'" class="mams-artlist-secimage-img"></div>';
	}
	echo '<div class="mams-secbycat-header">';
	if ($this->secinfo[0]->sec_desc) echo '<div class="mams-secbycat-header-secdesc">'.$this->secinfo[0]->sec_content.'</div>';
	echo '<div class="mams-secbycat-header-catlist">';
	$i = 0;
	foreach ($this->cats as $c) {
		if ($i==1) $i=0; else $i=1;
		echo '<div class="mams-secbycat-header-cat';
		if ($first) { echo ' mams-row-first'; $first = false; }
		echo ' mams-row'.($i % 2);
		echo '">';
		echo '<a href="#'.$c->cat_alias.'">'.$c->cat_title.'</a> ';
		if($this->params->get("show_count",0)) echo '<div class="mams-secbycat-header-cat-artcount">'.count($c->articles).'</div>';
		echo '</div>';
	}
	echo '</div>';
	echo '</div>';
} else {
	if ($this->params->get("show_page_heading",1)) {
		echo '<h1 class="title uk-article-title">';
		echo $this->params->get( "page_title", $app->getMenu()->getActive()->title );
		echo '</h1>';
	}
	if ($this->headerContent) {
		echo $this->headerContent;
	}
	echo '<div class="mams-secbycat-header">';
	echo '<div class="mams-secbycat-header-catlist">';
	foreach ($this->cats as $c) {
		if ($i==1) $i=0; else $i=1;
		echo '<div class="mams-secbycat-header-cat';
		if ($first) { echo ' mams-row-first'; $first = false; }
		echo ' mams-row'.($i % 2);
		echo '">';
		echo '<a href="#'.$c->cat_alias.'">'.$c->cat_title.'</a> ';
		if($this->params->get("show_count",0)) echo '<div class="mams-secbycat-header-cat-artcount">'.count($c->articles).'</div>';
		echo '</div>';
	}
	echo '</div>';
	echo '</div>';
}
echo '<div class="mams-secbycat-cats">';
foreach ($this->cats as $c) {
	echo '<div class="mams-secbycat-cat">';
	echo '<div class="mams-secbycat-cat-header"><a name="'.$c->cat_alias.'"></a>'.$c->cat_title.'<a class="mams-scroller" data-uk-smooth-scroll href="#"></a></div>';
	echo '<div class="mams-secbycat-cat-items">'; 
	$this->articles = $c->articles;
	include 'artlist.php';
	echo '</div></div>';
}
echo '</div>';
