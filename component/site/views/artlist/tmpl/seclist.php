<?php
defined('_JEXEC') or die();

$app = JFactory::getApplication();
echo '<h2 class="title uk-article-title">';
echo $this->params->get("page_title",$app->getMenu()->getActive()->title);
echo '</h2>';
echo '<div class="mams-seclist">';
$first=true;
$numcols = $this->params->get("list_cols",1);
$colwidth = (int)(100/$numcols);
$numpercol = ceil(count($this->seclist)/$numcols);
$count = 0;
foreach ($this->seclist as $s) {
	if ($count == $numpercol) { $count=0; echo '</div></div>';}
	if ($count == 0) { $i=0; $first=true; echo '<div style="float:left;width:'.$colwidth.'%"><div class="mams-seclist-col">'; }
	if ($i==1) $i=0; else $i=1;
	echo '<div class="mams-seclist-sec';
	echo ' mams-row'.($i % 2);
	if ($first) { echo ' mams-row-first'; $first = false; }
	echo '">';
	echo '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$s->sec_id.':'.$s->sec_alias).'">'.$s->sec_name.'</a> ';
	if($this->params->get("show_count",0)) echo '<div class="mams-seclist-sec-artcount">'.$s->numarts.'</div>';
	echo '</div>';
	$count = $count + 1;
}
echo '</div></div></div>';