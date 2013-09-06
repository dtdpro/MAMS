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
		
			
			
			//Further Article Details
			echo '<div class="mams-artlist-artdetails">';
				//Thumb
				if ($a->art_thumb) {
					echo '<div class="mams-artlist-artimg"><img class="mams-artlist-artthumb"';
					echo ' src="'.$a->art_thumb.'" ';
					echo ' /></div>';
				}
				//Article Pub info and description
				echo '<div class="mams-artlist-artinfo';
				if ($a->art_thumb) echo 'wt';
				echo '">';
					if ($this->params->get('show_pubinfo',1)) {
						echo '<div class="mams-artlist-pubinfo">';
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
						echo '</div>';
					}
				
					//Desc
					echo '<div class="mams-artlist-artdesc">';
						echo $a->art_desc;
					echo '</div>';
					
					//Additional Fields
					if ($a->fields) {
						echo '<div class="mams-artlist-artfields">';
						$curgroup = "";
						$first=true;
						foreach ($a->fields as $f) {
							$fn = $f->field_name;
							if ($a->art_fielddata->$fn || $f->data) {
								if ($f->group_name != $curgroup) {
									if (!$first) { echo '</div>';  }
									else { $first=false; }
									echo '<div class="mams-artlist-'.$f->group_name.'">';
									$curgroup = $f->group_name;
									if ($f->group_show_title) {
										echo '<div class="mams-artlist-'.$f->group_name.'-title">';
										if ($f->group_params->linktitlelist) echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.':'.$a->art_alias).'#'.$f->group_name.'">';
										echo $f->group_title;
										if ($f->group_params->linktitlelist) echo '</a>';
										echo '</div>';
									}
								}
								if ($f->field_params->show_title_desc) {
									echo '<div class="mams-artlist-'.$f->group_name.'-'.$f->field_name.'-title">';
									echo $f->field_title;
									echo '</div>';
								}
								echo '<div class="mams-artlist-'.$f->group_name.'-'.$f->field_name.'">';
								if ($f->field_params->pretext) echo $f->field_params->pretext.' ';
								switch ($f->field_type) {
									case "textfield":
										if ($f->field_params->linktext==1) echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.':'.$a->art_alias).'#'.$f->field_name.'">';
										if ($f->field_params->linktext==2) echo '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.':'.$a->art_alias).'#'.$f->group_name.'">';
										echo $a->art_fielddata->$fn;
										if ($f->field_params->linktext) echo '</a>';
										break;
									case "textbox":
									case "editor":
										echo $a->art_fielddata->$fn;
										break;
									case "auths":
										$auts = Array();
										foreach ($f->data as $d) {
											$auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$d->auth_sec."&autid=".$d->auth_id.":".$d->auth_alias).'" class="mams-artlist-'.$f->group_name.'-'.$f->field_name.'-autlink">'.$d->auth_fname.(($d->auth_mi) ? " ".$d->auth_mi : "")." ".$d->auth_lname.(($d->auth_titles) ? ", ".$d->auth_titles : "").'</a>';
										}
										echo implode(", ",$auts);
										break;
									case "dloads":
										$firstdl=true;
										foreach ($f->data as $d) {
											echo '<div class="mams-artlist-'.$f->group_name.'-'.$f->field_name.'-dload">';
											echo '<a href="'.JRoute::_("components/com_mams/dl.php?dlid=".$d->dl_id).'" ';
											echo 'target="_blank" ';
											echo 'class="mams-artlist-'.$f->group_name.'-'.$f->field_name.'-artdload';
											if ($firstdl) { echo ' firstdload'; $firstdl=false; }
											echo '">';
											echo 'Download '.$d->dl_lname;
											echo '</a>';
											echo '</div>';
										}
										break;
									case "links":
										$firstlink=true;
										foreach ($f->data as $d) {
											echo '<div class="mams-artlist-'.$f->group_name.'-'.$f->field_name.'-link">';
											echo '<a href="'.$d->link_url.'" ';
											echo 'target="'.$d->link_target.'" ';
											echo 'class="mams-artlist-'.$f->group_name.'-'.$f->field_name.'-artlink';
											if ($firstlink) { echo ' firstlink'; $firstlink=false; }
											echo '">';
											echo $d->link_title;
											echo '</a>';
											echo '</div>';
										}
										break;
								}
								if ($f->field_params->posttext) echo ' '.$f->field_params->posttext;
								echo '</div>';
							}
						}
						if (!$first) echo '</div>';
						echo '</div>';
					}
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
	
	echo '</div>';
	echo '<div class="mams-artlist-seperator"></div>';
	//echo '<pre>'; print_r($a); echo '</pre>';
	//echo '<div class="mams-artlist-seperator"></div>';

}
echo '<div class="mams-artlist-pagination">';
echo '<div class="mams-artlist-pagination-links">';
echo $this->pagination->getPagesLinks();
echo '</div>';
echo '<div class="mams-artlist-pagination-pages">';
echo $this->pagination->getPagesCounter().'<br />'.$this->pagination->getResultsCounter();
echo '</div>';
echo '</div>';