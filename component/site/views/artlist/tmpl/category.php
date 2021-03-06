<?php
defined('_JEXEC') or die();
if ($this->params->get('divwrapper',1)) {
	echo '<div id="system" class="'.$this->params->get('wrapperclass','uk-article').'">';
}
$app = JFactory::getApplication();
if (count($this->catinfo) == 1) {
	if ($this->params->get("show_page_heading",1)) {
		echo '<h1 class="title uk-article-title">';
		echo $this->catinfo[0]->cat_title;
		echo '</h1>';
	}
	if ($this->headerContent) {
		echo $this->headerContent;
	}
	if ($this->params->get("show_catimage",0)) {
		echo '<div class="mams-artlist-catimage"><img src="'.$this->catinfo[0]->cat_image.'" class="mams-artlist-catimage-img"></div>';
	}
	echo '<div class="mams-artlist-catdesc">'.$this->catinfo[0]->cat_desc.'</div>';
} else if ($this->params->get("show_page_heading",1)) {
	echo '<h1 class="title uk-article-title">';
	echo $this->params->get("page_title",$app->getMenu()->getActive()->title);
	echo '</h1>';
	if ($this->headerContent) {
		echo $this->headerContent;
	}
} else {
	if ($this->headerContent) {
		echo $this->headerContent;
	}
}
if (count($this->childcatlist)) {
	echo '<div class="mams-catlist">';
	$first     = true;
	$numcols   = $this->params->get( "list_cols", 1 );
	$colwidth  = (int) ( 100 / $numcols );
	$numpercol = ceil( count( $this->childcatlist ) / $numcols );
	$count     = 0;
	foreach ( $this->childcatlist as $c ) {
		if ( $count == $numpercol ) {
			$count = 0;
			echo '</div></div>';
		}
		if ( $count == 0 ) {
			$i     = 0;
			$first = true;
			echo '<div style="float:left;width:' . $colwidth . '%"><div class="mams-catlist-col">';
		}
		if ( $i == 1 ) {
			$i = 0;
		} else {
			$i = 1;
		}
		echo '<div class="mams-catlist-cat';
		echo ' mams-row' . ( $i % 2 );
		if ( $first ) {
			echo ' mams-row-first';
			$first = false;
		}
		echo '">';
		echo '<a href="' . JRoute::_( "index.php?option=com_mams&view=artlist&layout=category&catid=" . $c->cat_id . ':' . $c->cat_alias ) . '">' . $c->cat_title . '</a> ';
		if ( $this->params->get( "show_count", 0 ) ) {
			echo '<div class="mams-catlist-cat-artcount">' . $c->numarts . '</div>';
		}
		echo '</div>';
		$count = $count + 1;
	}
	echo '</div></div></div>';
}
