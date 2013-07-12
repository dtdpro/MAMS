<?php
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
			require_once('components/com_mams/helpers/mams.php');
			$mamscfg = MAMSHelper::getConfig();
			$doc = &JFactory::getDocument();
			JHtml::_('jquery.framework');
			$doc->addScript('media/com_mams/mediaelementjs/mediaelement-and-player.js');
			$doc->addStyleSheet('media/com_mams/mediaelementjs/mediaelementplayer.css');
			
			$output .= '<div class="mams-plugin-media">';
			$output .= '<div align="center">';
			$output .=  '<div class="mams-plugin-mediawrap"';
			if ($mamscfg->player_fixed) $output .=  ' style="width: '.(int)$mamscfg->vid_w.'px;"';
			$output .=  '>';
			if ($media->med_type == 'vid' || $media->med_type == 'vids') { //Video Player
				$output .=  '<div class="mams-plugin-media-player';
				if (count($items) == 1) $output .=  'one';
				else if ($mamscfg->player_fixed) $output .=  'fixed';
				$output .=  '">';
				$output .=  '<video width="'.(int)$mamscfg->vid_w.'" height="'.(int)$mamscfg->vid_h.'" ';
				if (!$mamscfg->player_fixed) $output .=  'style="width: 100%; height: 100%;" ';
				$output .=  'id="mams-plugin-mediaelement'.$media->med_id.'" src="http://'.$mamscfg->vid5_url.'/'.$media->med_file.'" type="video/mp4" controls="controls" poster="'.$media->med_still.'"></video>';
				$output .=  '<script type="text/javascript">';
				$output .=  "var fmplayer = new MediaElementPlayer('#mams-plugin-mediaelement".$media->med_id."');";
				$output .=  '</script>';
				$output .=  '</div>';
			}
			
			if ($media->med_type == 'aud') { //Audio Player
				$output .=  '<div class="mams-plugin-media-player';
				if (count($items) == 1) $output .=  'one';
				else if ($mamscfg->player_fixed) $output .=  'fixed';
				$output .=  '">';
				$output .=  '<audio width="'.(int)$mamscfg->vid_w.'"  ';
				if (!$$mamscfg->player_fixed) $output .=  'style="width: 100%;" ';
				$output .=  'id="mams-plugin-mediaelement'.$media->med_id.'" src="'.JURI::base( true ).'/'.$media->med_file.'" type="audio/mp3" controls="controls"></audio>';
				$output .=  '<script type="text/javascript">';
				$output .=  "var fmplayer = new MediaElementPlayer('#mams-plugin-mediaelement".$media->med_id."');";
				$output .=  '</script>';
				$output .=  '</div>';
			}
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
		}
		
		return $output;
	}
}




?>
