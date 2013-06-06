<?php
// no direct access
defined('_JEXEC') or die;
$config=MAMSHelper::getConfig();

//Media
if ($items) {
	echo '<div class="mams-mod-featmedia"';
	if ($params->get("player_fixed",0)) echo ' style="width: '.(int)$params->get('player_w','512').'px;"';
	echo '>';
	if ($items[0]->med_type == 'vid' || $items[0]->med_type == 'vids') { //Video Player
		echo '<div class="mams-mod-featmedia-player';
		if (count($items) == 1) echo 'one';
		else if ($params->get("player_fixed",0)) echo 'fixed';
		echo '">';
		echo '<video width="'.(int)$params->get('player_w','512').'" height="'.(int)$params->get('player_h','288').'" ';
		if (!$params->get("player_fixed",0)) echo 'style="width: 100%; height: 100%;" ';
		echo 'id="mams-featmedia-mediaelement" src="http://'.$config->vid5_url.'/'.$items[0]->med_file.'" type="video/mp4" controls="controls" poster="'.$items[0]->med_still.'"></video>';
		echo '<script type="text/javascript">';
		echo "var fmplayer = new MediaElementPlayer('#mams-featmedia-mediaelement');"; 
		echo '</script>';
		if (count($items) > 1) {
		?>
			<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery(".mams-mod-featmedia-item").click(function(){	
					var parent = jQuery(this).parents('.mams-mod-featmedia-playlist');	
					jQuery('.mams-mod-featmedia-item',parent).removeClass('selected');
					jQuery(this).addClass('selected');
				}); <?php 
				foreach ($items as $m) {
					echo 'jQuery(document).on("click", ".mfmpli-'.$m->med_id.'",function(e){';
				    echo "fmplayer.pause();fmplayer.setSrc('http://".$config->vid5_url.'/'.$m->med_file."');fmplayer.play();";
					echo '}); ';
				}?>
			});
			</script>
		<?php 
		}
		echo '</div>';
		if (count($items) > 1) {
			echo '<div class="mams-mod-featmedia-playlist';
			if ($params->get("player_fixed",0)) echo 'fixed';
			echo '">';
			echo '<ul>';
			$first = true;
			foreach ($items as $m) {
				echo '<li class="mams-mod-featmedia-item mfmpli-'.$m->med_id;
				if ($first) { echo ' selected'; $first=false; }
				echo '">';
				echo "";
				echo '<div class="mfmpli-thumb"><img src="'.$m->med_still.'" border="0" class="size-auto"></div>';
				echo '<div class="mfmpli-text"><div class="mfmpli-title">'.$m->med_exttitle.'</div><div class="mfmpli-desc">'.$m->med_desc;
				echo '</div></div>';
				echo '</li>';
			}
			echo '</ul>';
			echo '</div>';
		}
	}
	echo '</div>';
	echo '<div style="clear:both"></div>';
}