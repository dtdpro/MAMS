<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class  plgContentMAMSCat extends JPlugin
{

	public function onContentPrepare($context, &$article, &$params, $limitstart) {
		$regex = "#{mamscat}(.*?){/mamscat}#s";
		$plugin =&JPluginHelper::getPlugin('content', 'MAMSCat');
		if (!$plugin->published){ 
			//plugin not published 
		}else  { 
			//plugin published 
		}
		$matched = preg_match_all( $regex, $article->text, $matches, PREG_SET_ORDER );
		if ($matches) {
			foreach ($matches as $match) {
				
				$matcheslist =  explode(',',$match[1]);
		
				$cid = trim($matcheslist[0]);
			
				if ($cid) $new=$this->MAMSCatReplacer($cid);
				$article->text = preg_replace("|$match[0]|", $new, $article->text, 1);	
			}
		}
	}
	
	function MAMSCatReplacer ( $cid ) {
		require_once('components/com_mams/helpers/mams.php');
		require_once('components/com_mams/router.php');
		
		$db =& JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$alvls = Array();
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
		
		$query->select('ac.ac_art');
		$query->from('#__mams_artcat AS ac');
		$query->where('ac.ac_cat = '.(int)$cid);
		$query->where('ac.published >= 1');
		$db->setQuery($query);
		$artids = $db->loadColumn(0);
		
		if ($artids) {
			$query = $db->getQuery(true);
			$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
			$query->from('#__mams_articles AS a');
			$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
			$query->where('a.art_id IN ('.implode(",",$artids).')');
			$query->where('a.state >= 1');
			$query->where('a.access IN ('.implode(",",$alvls).')');
			if (!in_array($cfg->ovgroup,$alvls)) { $query->where('a.art_publish_up <= NOW()'); $query->where('(a.art_publish_down >= NOW() || a.art_publish_down="0000-00-00")'); }
			$query->order('a.art_publish_up DESC, s.ordering ASC, a.ordering ASC');
			$limitstart = 0;
			$limit = 5;
			
			$db->setQuery($query, $limitstart, $limit);
			$items = $db->loadObjectList();
			
			$output = "";
			$output .=  '<ul>';
			foreach ($items as $a) {
				$output .= '<li>';
				$output .= '<span class="mams-catplug-title">';
				$output .= '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.":".$a->art_alias).'">';
				$output .= $a->art_title;
				$output .= '</a></span>';
	
				$output .= '</li>';
			}
			$output .= '</ul>';
		}
		return $output;
	}
}




?>
