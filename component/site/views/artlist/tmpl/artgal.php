<?php
defined('_JEXEC') or die();

echo '<div class="mams-artgal">';
foreach ($this->articles as $a) {
	echo '<div class="mams-artgal-article">';
	
		//Thumb
		if ($a->art_thumb) {
			echo '<div class="mams-artgal-artimg">';
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.":".$a->art_alias).'" class="mams-artgal-art-imglink">';
			echo '<img class="mams-artgal-imgthumb"';
			echo ' src="'.$a->art_thumb.'" ';
			echo ' /></a></div>';
		}
		
		//Title
		echo '<div class="mams-artgal-art-info">';
			echo '<div class="mams-artgal-art-title">';
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.":".$a->art_alias).'" class="mams-artgal-art-link">'.$a->art_title.'</a>';
			echo '</div>';
			echo '<div class="mams-artgal-art-details">';
			echo $a->art_desc;
			echo '</div>';
		echo '</div>';
	echo '</div>';


}
echo '</div>';
echo '<div class="mams-artlist-pagination">';
echo '<div class="mams-artlist-pagination-links">';
echo $this->pagination->getPagesLinks();
echo '</div>';
echo '<div class="mams-artlist-pagination-pages">';
echo $this->pagination->getPagesCounter().'<br />'.$this->pagination->getResultsCounter();
echo '</div>';
echo '</div>';