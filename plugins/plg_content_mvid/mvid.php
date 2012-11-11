<?php
/**
 * MAMS Video plugin for Content
 * @license http://www.gnu.org/licenses/gpl.html GNU/GPL.
 * @by Mike Amundsen
 * @Copyright (C) 2012 
  */
defined( '_JEXEC' ) or die( 'Restricted access' );

class  plgContentMVid extends JPlugin
{

	public function onContentPrepare($context, &$article, &$params, $limitstart) {
		$regex = "#{mvid}(.*?){/mvid}#s";
		$plugin =&JPluginHelper::getPlugin('content', 'MVid');
		if (!$plugin->published){ 
			//plugin not published 
		}else  { 
			//plugin published 
		}
		$matched = preg_match_all( $regex, $article->text, $matches, PREG_SET_ORDER );
		if ($matches) {
			foreach ($matches as $match) {
				
				$matcheslist =  explode(',',$match[1]);
		
				$vid = trim($matcheslist[0]);
			
				$newvid=$this->MVidReplacer($vid);
				$article->text = preg_replace("|$match[0]|", $newvid, $article->text, 1);	
			}
		}
	}
	
	function MVidReplacer ( $vid ) {
		
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		
		$qm=$db->getQuery(true);
		$qm->select('*');
		$qm->from('#__mams_media');
		$qm->where('published >= 1');
		$qm->where('access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$qm->where('med_id = '.$vid);
		$db->setQuery($qm);
		$media=$db->loadObject();
		
		$output = "";
		if ($media) {
			require_once('components'.DS.'com_mams'.DS.'helpers'.DS.'mams.php');
			$mamscfg = MAMSHelper::getConfig();
			$doc = &JFactory::getDocument();
			$doc->addScript('media/com_mams/vidplyr/jwplayer.js');
			$doc->addScript('media/com_mams/scripts/mams.js');
			$doc->addScriptDeclaration("var mamsuri = '".JURI::base( true )."';");
			$output .= '<div class="continued-material-media">';
			$output .= '<div align="center">';
			if ($media->med_type == 'vid' || $media->med_type == 'vids') { //Video Player
				//flash player
				$output .= '<div id="mediaspace"></div>'."\n";
				$output .= "<script type='text/javascript'>"."\n";
				$output .= "jwplayer('mediaspace').setup({"."\n";
		 		if ($media->med_type == "vid") {
		   			$output .= "'flashplayer': '".JURI::base( true )."/media/com_mams/vidplyr/player.swf',"."\n";
		 			$output .= "'file': '".JURI::base( true ).'/'.$media->med_file."',"."\n";
		 		}
		 		if ($media->med_type == "vids") {
		 			$output .= "'modes':[";
		 			$output .= "{ type: 'flash',\n";
		   			$output .= "'src': '".JURI::base( true )."/media/com_mams/vidplyr/player.swf',"."\n";
		 			$output .= "'config':{\n";
		   			$output .= "'provider': 'rtmp',"."\n";
		 			$output .= "'streamer': 'rtmp://".$mamscfg->vids_url.'/'.$mamscfg->vids_app.'/'."',"."\n";
		 			$output .= "'file':'mp4:".$media->med_file."',"."\n";
		 			$output .= "}},\n";
		 			$output .= "{ type: 'html5',\n";
		 			$output .= "'config':{\n";
		 			$output .= "'file':'http://".$mamscfg->vid5_url."/".$media->med_file."',"."\n";
		 			$output .= "}}\n";
		 			$output .= "],\n";
		 		}
				$output .= "'image': '".JURI::base( true ).'/'.$media->med_still."',"."\n";
				$output .= "'frontcolor': '000000',"."\n";
				$output .= "'lightcolor': 'cc9900',"."\n";
				$output .= "'screencolor': '000000',"."\n";
				$output .= "'skin': '".JURI::base( true )."/media/com_mams/vidplyr/glow/glow.xml',"."\n";
				$output .= "'controlbar': 'bottom',"."\n";
				$output .= "'width': '".$mamscfg->vid_w."',"."\n";
				$output .= "'height': '".((int)$mamscfg->vid_h+30)."'";
				$output .= ",\n'plugins': {'".JURI::base( true )."/media/com_mams/vidplyr/mamstrack.js': {'itemid':".$media->med_id."}";
				if ($mamscfg->gapro) $output .= ",'".JURI::base( true )."/media/com_mams/vidplyr/mamsga.js': {}";
				$output .= "}"."\n";
				$output .= "});"."\n";
				$output .= "</script>"."\n";
			}
			if ($media->med_type == 'aud') { //Audio Player
				$output .= '<div id="mediaspace"></div>'."\n";
				$output .= '<script type="text/javascript">'."\n";
				$output .= "jwplayer('mediaspace').setup({"."\n";
				$output .= "'width': '".$mamscfg->aud_w."',"."\n";
				$output .= "'height': '".((int)$mamscfg->aud_h+30)."',"."\n";
				$output .= "'file': '".JURI::base( true ).'/'.$media->med_file."',"."\n";
				$output .= "'image': '".JURI::base( true ).'/'.$media->med_still."',"."\n";
				$output .= "'frontcolor': '000000',"."\n";
				$output .= "'lightcolor': 'cc9900',"."\n";
				$output .= "'screencolor': '000000',"."\n";
				$output .= "'skin': '".JURI::base( true )."/media/com_mams/vidplyr/glow/glow.xml',"."\n";
				$output .= "'controlbar': 'bottom',"."\n";
				$output .= "'modes': [{type: 'flash', src: '".JURI::base( true )."/media/com_mams/vidplyr/player.swf'},{type: 'html5'},{type: 'download'}]"."\n";
				$output .= ",\n'plugins': {'".JURI::base( true )."/media/com_mams/vidplyr/mamstrack.js': {'itemid':".$media->med_id."}";
				if ($mamscfg->gapro)	$output .= ",'".JURI::base( true )."/media/com_mams/vidplyr/mamsga.js': {}";
				$output .= "}"."\n";
				$output .= "});"."\n";
				$output .= "</script>"."\n";
			}
			$output .= '</div>';
			$output .= '</div>';
		}
		
		return $output;
	}
}




?>
