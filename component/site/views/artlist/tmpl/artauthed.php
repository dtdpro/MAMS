<?php
defined('_JEXEC') or die();
if ($this->params->get('divwrapper',1)) {
	echo '<div id="system" class="'.$this->params->get('wrapperclass','uk-article').'">';
}
$app = JFactory::getApplication();
	if ($this->params->get("show_page_heading",1)) {
		$auths = $this->authors;
		if (count($auths) > 1) {
			echo '<h1 class="title uk-article-title">Additional Articles by the Authors</h1>';
		} else {
			echo '<h1 class="title uk-article-title">Additional Articles by the Author</h1>';
		}
		$authbyrow=$this->params->get('auth_byrow',2);
		$authspan = "span".(12/$authbyrow);
		$authcount=0;
		echo '<div class="mams-article-article-art-auths">';
		echo '<div class="mams-article-article-art-auths-authrow mams-article-authrow row-fluid">';
		foreach ($auths as $d) {
			echo '<div class="mams-article-article-art-auths-auth mams-article-auth '.$authspan.'">';
			echo '<div class="mams-article-article-art-auths-auth-name mams-article-auth-name">';
			echo '<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$d->auth_sec."&autid=".$d->auth_id.":".$d->auth_alias).'" class="mams-article-article-art-auths-autlink">';
			echo $d->auth_fname.(($d->auth_mi) ? " ".$d->auth_mi : "")." ".$d->auth_lname.(($d->auth_titles) ? ", ".$d->auth_titles : "").'</a>';
			echo '</div>';
			echo '<div class="mams-article-article-art-auths-auth-cred mams-article-auth-cred">';
			if ($this->params->get('show_authcred',1)) echo $d->auth_credentials;
			echo '</div></div>';
			$authcount++;
			if ($authcount == $authbyrow) {
				echo '</div>';
				echo '<div class="mams-article-article-art-auths-authrow mams-article-authrow row-fluid">';
				$authcount=0;
			}
		}
		echo '</div>';
		echo '</div>';
	}


