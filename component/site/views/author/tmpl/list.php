<?php 
defined('_JEXEC') or die();
if ($this->params->get('divwrapper',1)) {
	echo '<div id="system" class="'.$this->params->get('wrapperclass','uk-article').'">';
}
echo '<h2 class="title uk-article-title">';
echo $this->params->get("page_title",'Authors'); 
echo '</h2>';

//Authors
if ($this->autlist) {
	echo '<div class="mams-author-auths">';
	foreach ($this->autlist as $s) {
		if ($s->authors) {
			echo '<div class="mams-author-authsec">';
				if (count($this->autlist) > 1) echo '<div class="mams-author-authsec-title">'.$s->sec_name.'</div>';
				echo '<div class="mams-author-authsec-auths">';
					foreach ($s->authors as $f) {
						if ($this->params->get("show_aimg",0) || $this->params->get("show_cred",0)) echo '<div class="mams-author-authitem">';
							if ($f->auth_image && $this->params->get("show_aimg",0)) {
								echo '<div class="mams-author-authimg"><img class="mams-author-listimage"';
								echo ' src="'.$f->auth_image.'" ';
								echo ' /></div>';
							}
							echo '<div class="mams-author-authinfo"><div class="mams-author-authname">';
							if ($this->params->get("show_authlink",1)) echo '<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$f->auth_sec.":".$f->sec_alias."&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-article-autlink">';
							echo $f->auth_fname.(($f->auth_mi) ? " ".$f->auth_mi : "")." ".$f->auth_lname.(($f->auth_titles) ? ", ".$f->auth_titles : "");
							if ($this->params->get("show_authlink",1)) echo '</a>';
							echo '</div>';
							
							//Credentials
							if ($this->params->get("show_cred",0) && $f->auth_credentials) echo '<div class="mams-author-authcred">'.$f->auth_credentials.'</div>';

							//Description
							if ($this->params->get("show_desc",1)) echo '<div class="mams-author-authdesc">'.$f->metadesc.'</div>';
							
							//Read More
							if ($this->params->get('show_readmore',1)) {
								echo '<div class="mams-author-authreadmore">';
								echo '<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$f->auth_sec.":".$f->sec_alias."&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-author-authlink read-more uk-button">';
								echo $this->params->get('readmore_text',"Read More");
								echo '</a>';
								echo '</div>';
							}
							echo '</div>';
						if ($this->params->get("show_aimg",0) || $this->params->get("show_cred",0)) echo '</div>';
					}
				echo '</div>';
			echo '</div>';
			echo '<div style="clear:both"></div>';
		}
	}
	echo '</div>';
}
if ($this->params->get('divwrapper',1)) { echo '</div>'; }