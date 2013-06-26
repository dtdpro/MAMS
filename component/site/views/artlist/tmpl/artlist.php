<?php
defined('_JEXEC') or die();
foreach ($this->articles as $a) {
	echo '<div class="mams-artlist-article">';
	
		//Title
		echo '<div class="mams-artlist-arttitle">';
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.":".$a->art_alias).'" class="mams-artlist-artlink">'.$a->art_title.'</a>';
		echo '</div>';
		
		//Authors
		if ($a->auts) {
			echo '<div class="mams-artlist-artaut">';
				$auts = Array();
				foreach ($a->auts as $f) {
					$auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$f->auth_sec."&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-artlist-autlink">'.$f->auth_fname.(($f->auth_mi) ? " ".$f->auth_mi : "")." ".$f->auth_lname.(($f->auth_titles) ? ", ".$f->auth_titles : "").'</a>';
				}
				echo implode(", ",$auts);
			echo '</div>';
		}
		
		echo '<div class="mams-artlist-arttpdrm">';
		
			echo '<div class="mams-artlist-artpubdesc">';
			
				//Thumb, Section, Pub Date & Cat
				echo '<div class="mams-artlist-artpubcat">';
					//Thumb
					if ($a->art_thumb) {
						echo '<img class="mams-artlist-artthumb"';
						echo ' src="'.$a->art_thumb.'" ';
						echo 'align="left" />';
					}
					
					if ($this->params->get('show_pubinfo',1)) {
						//Section Link
						echo '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$a->sec_id.":".$a->sec_alias).'" class="mams-artlist-seclink">'.$a->sec_name.'</a>';
						
						//Pub Date
						if ($this->params->get('show_pubdate',1)) {
							echo ' published on <strong>';
							echo date("F j, Y",strtotime($a->art_publish_up));
							echo '</strong>';
						}
						
						//Cat Links
						if ($a->cats) {
							if ($this->params->get('show_pubdate',1)) {
								echo ' in <em>';
							} else {
								echo ' - <em>';
							}
							$cats = Array();
							foreach ($a->cats as $c) {
								$cats[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=category&secid=".$a->sec_id.":".$a->sec_alias."&catid=".$c->cat_id.":".$c->cat_alias).'" class="mams-artlist-catlink">'.$c->cat_title.'</a>';
							}
							echo implode(", ",$cats);
							echo '</em>';
						}
					}
				echo '</div>';
				
				//Desc
				echo '<div class="mams-artlist-artdsec">';
					echo $a->art_desc;
				echo '</div>';
			
			echo '</div>';
			
			//Read More
			if ($this->params->get('show_readmore',1)) {
				echo '<div class="mams-artlist-artreadmore">';
				echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.':'.$a->art_alias).'" class="mams-artlist-artlink read-more">';
				echo $this->params->get('readmore_text',"Read More");
				echo '</a>';
				echo '</div>';
			}
		echo '</div>';
	
	echo '</div>';
	echo '<div class="mams-artlist-seperator"></div>';

}
echo '<div class="mams-artlist-pagination">';
echo '<div class="mams-artlist-pagination-links">';
echo $this->pagination->getPagesLinks();
echo '</div>';
echo '<div class="mams-artlist-pagination-pages">';
echo $this->pagination->getPagesCounter().'<br />'.$this->pagination->getResultsCounter();
echo '</div>';
echo '</div>';