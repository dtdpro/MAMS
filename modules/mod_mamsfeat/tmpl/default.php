<?php

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
echo '<ul>';
foreach ($articles as $a) {
	echo '<li>';
	echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.":".$a->art_alias).'">';
	echo $a->art_title;
	echo '</a><br />';
	if ($a->auts) {
		$auts = Array();
		foreach ($a->auts as $f) {
			//$auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-artlist-autlink">'.$f->auth_name.'</a>';
			$auts[]=$f->auth_name;
		}
		echo implode(", ",$auts);
	}
	echo '</li>';
}
echo '</ul>';