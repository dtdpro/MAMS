<?php

// no direct access
defined('_JEXEC') or die;
$config=MAMSHelper::getConfig();

//Media
if ($items) {
	echo '<div class="mams-mod-featmedia">';
	echo '<div align="center">';
	if ($items[0]->med_type == 'vid' || $items[0]->med_type == 'vids') { //Video Player
		//flash player
		echo '<div id="featmediaspace"></div>'."\n";
		echo "<script type='text/javascript'>"."\n";
		echo "jwplayer('featmediaspace').setup({"."\n";
		if ($items[0]->med_type == "vid") {
			echo "'flashplayer': '".JURI::base( true )."/media/com_mams/vidplyr/player.swf',"."\n";
			echo "'file': '".JURI::base( true ).'/'.$items[0]->med_file."',"."\n";
		}
		if ($items[0]->med_type == "vids") {
			echo "'modes':[";
			echo "{ type: 'flash',\n";
			echo "'src': '".JURI::base( true )."/media/com_mams/vidplyr/player.swf',"."\n";
			echo "'config':{\n";
			if (count($items) == 1) {
				echo "'provider': 'rtmp',"."\n";
				echo "'streamer': 'rtmp://".$config->vids_url.'/';
				if ($config->vids_app) echo $config->vids_app.'/';
				echo "',"."\n";
				echo "'file':'mp4:".$items[0]->med_file."',"."\n";
			} else {
				echo "'playlist': ["."\n";
				foreach ($items as $m) {
					echo "{"."\n";
					echo "'provider': 'rtmp',"."\n";
					echo "'streamer': 'rtmp://".$config->vids_url.'/';
					if ($config->vids_app) echo $config->vids_app.'/';
					echo "',"."\n";
					echo "'file':'mp4:".$m->med_file."',"."\n";
					echo "'image': '".JURI::base( true ).'/'.$m->med_still."',"."\n";
					echo "'title': '".JURI::base( true ).$m->med_exttitle."',"."\n";
					echo "'description': '".JURI::base( true ).$m->med_desc."',"."\n";
					echo "},"."\n";
				}
				echo "],"."\n";
			}
			echo "}\n";
			echo "},\n";
			echo "{ type: 'html5',\n";
			echo "'config':{\n";
			if (count($items) == 1) {
				echo "'file':'http://".$config->vid5_url."/".$items[0]->med_file."',"."\n";
			} else {
				echo "'playlist': ["."\n";
				foreach ($items as $m) {
					echo "{"."\n";
					echo "'file':'http://".$config->vid5_url."/".$m->med_file."',"."\n";
					echo "'image': '".JURI::base( true ).'/'.$m->med_still."',"."\n";
					echo "'title': '".JURI::base( true ).$m->med_exttitle."',"."\n";
					echo "'description': '".JURI::base( true ).$m->med_desc."',"."\n";
					echo "},"."\n";
				}
				echo "],"."\n";
			}
			echo "}\n";
			echo "}\n";
			echo "],\n";
		}
		if (count($items) == 1) echo "'image': '".JURI::base( true ).'/'.$items[0]->med_still."',"."\n";
		echo "'frontcolor': '000000',"."\n";
		echo "'lightcolor': 'cc9900',"."\n";
		echo "'screencolor': '000000',"."\n";
		echo "'skin': '".JURI::base( true )."/media/com_mams/vidplyr/glow/glow.xml',"."\n";
		echo "'controlbar': 'bottom',"."\n";
		//if ($items[0]->med_autoplay) echo "'autostart':'true',"."\n";
		if (count($items) > 1 && $params->get('playlist_loc','right') == "right") echo "'width': '".((int)$params->get('player_w','512')+(int)$params->get('playlist_size','300'))."',"."\n";
		else echo "'width': '".(int)$params->get('player_w','512')."',"."\n";
		if (count($items) > 1 && $params->get('playlist_loc','right') == "bottom") echo "'height': '".((int)$params->get('player_h','288')+30+(int)$params->get('playlist_size','300'))."'";
		else echo "'height': '".((int)$params->get('player_h','288')+30)."'";
		if (count($items) > 1) {
			echo ",'playlist.position': '".$params->get('playlist_loc','right')."',"."\n";
			echo "'playlist.size': '".$params->get('playlist_size','300')."'";
		}
		echo ",\n'plugins': {'".JURI::base( true )."/media/com_mams/vidplyr/mamstrack.js': {'itemid':".$items[0]->med_id."}";
		if ($config->gapro)	echo ",'".JURI::base( true )."/media/com_mams/vidplyr/mamsga.js': {}";
		echo "}"."\n";
		echo "});"."\n";
		echo "</script>"."\n";
	}
	echo '</div>';
	echo '</div>';
}