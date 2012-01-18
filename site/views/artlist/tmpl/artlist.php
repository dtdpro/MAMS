<?php
defined('_JEXEC') or die();
foreach ($this->articles as $a) {
	echo '<div class="mams-artlist-article">';
	
		//Title
		echo '<div class="mams-artlist-arttitle">';
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&id=".$a->art_id.":".$a->art_alias).'">'.$a->art_title.'</a>';
		echo '</div>';
		
		//Authors
		echo '<div class="mams-artlist-artaut">';
			$auts = Array();
			foreach ($a->auts as $f) {
				$auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&id=".$f->auth_id.":".$f->auth_alias).'">'.$f->auth_name.'</a>';
			}
			echo implode(", ",$auts);
		echo '</div>';
		
		echo '<div class="mams-artlist-arttpdrm">';
		
			echo '<div class="mams-artlist-artpubdesc">';
			
				//Pub Date & Cat
				echo '<div class="mams-artlist-artpubcat">';
					//Thumb
					if ($a->art_thumb) {
						echo '<img class="mams-artlist-artthumb"';
						echo ' src="'.$a->art_thumb.'" ';
						echo 'align="left" />';
					}
					echo 'Published on <strong>';
					echo date("F j, Y",strtotime($a->art_published));
					echo '</strong> in <em>';
					$cats = Array();
					foreach ($a->cats as $c) {
						$cats[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=category&id=".$c->cat_id.":".$c->cat_alias).'">'.$c->cat_title.'</a>';
					}
					echo implode(", ",$cats);
				echo '</em></div>';
				
				//Desc
				echo '<div class="mams-artlist-artdsec">';
					echo $a->art_desc;
				echo '</div>';
			
			echo '</div>';
			
			//Read More
			echo '<div class="mams-artlist-artreadmore">';
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&id=".$a->art_id).'">Read More</a>';
			echo '</div>';
		
		echo '</div>';
	
	echo '</div>';
	echo '<div class="mams-artlist-seperator"></div>';
	
	
}