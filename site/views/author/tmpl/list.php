<?php 
defined('_JEXEC') or die();
echo '<h2 class="title">';
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
								echo 'align="left" /></div>';
							}
							echo '<div class="mams-author-authname"><a href="'.JRoute::_("index.php?option=com_mams&view=author&autid=".$f->auth_id.":".$f->auth_alias).'" ';
							echo 'class="mams-article-autlink">';
							echo $f->auth_name;
							echo '</a></div>';
							if ($this->params->get("show_cred",0) && $f->auth_credentials) echo '<div class="mams-author-authcred">'.$f->auth_credentials.'</div>';
						if ($this->params->get("show_aimg",0) || $this->params->get("show_cred",0)) echo '</div>';
					}
				echo '</div>';
			echo '</div>';
			echo '<div style="clear:both"></div>';
		}
	}
	echo '</div>';
}