<?php
/**
 * MAMS Video plugin for Content
 * @license http://www.gnu.org/licenses/gpl.html GNU/GPL.
 * @by Mike Amundsen
 * @Copyright (C) 2012 
  */
defined( '_JEXEC' ) or die( 'Restricted access' );

class  plgContentMAMSCat extends JPlugin
{

	public function onContentPrepare($context, &$article, &$params, $limitstart) {
		$regex = "#{mamscat}(.*?){/mamscat}#s";
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
		
				$cid = trim($matcheslist[0]);
			
				$new=$this->MAMSCatReplacer($cid);
				$article->text = preg_replace("|$match[0]|", $new, $article->text, 1);	
			}
		}
	}
	
	function MAMSCatReplacer ( $cid ) {
require_once('components'.DS.'com_mams'.DS.'helpers'.DS.'mams.php');
require_once('components'.DS.'com_mams'.DS.'router.php');
		
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
		$catids = $db->loadResultArray(0);
		
		$query = $db->getQuery(true);
		$query->select('a.*,s.sec_id,s.sec_name,s.sec_alias');
		$query->from('#__mams_articles AS a');
		$query->join('RIGHT','#__mams_secs AS s ON s.sec_id = a.art_sec');
		$query->where('a.art_id IN ('.implode(",",$catids).')');
		$query->where('a.published >= 1');
		$query->where('a.access IN ('.implode(",",$alvls).')');
		if (!in_array($cfg->ovgroup,$alvls)) $query->where('a.art_published <= NOW()');
		$query->order('a.art_published DESC');
		$limitstart = 0;
		$limit = 5;
		
		$db->setQuery($query, $limitstart, $limit);
		$items = $db->loadObjectList();
				
		//Get Authors
		foreach ($items as &$i) {
			$qa=$db->getQuery(true);
			$qa->select('a.auth_id,a.auth_name,a.auth_alias,a.auth_sec');
			$qa->from('#__mams_artauth as aa');
			$qa->join('RIGHT','#__mams_authors AS a ON aa.aa_auth = a.auth_id');
			$qa->where('aa.published >= 1');
			$qa->where('a.published >= 1');
			$qa->where('a.access IN ('.implode(",",$alvls).')');
			$qa->where('aa.aa_art = '.$i->art_id);
			$qa->order('aa.ordering ASC');
			$db->setQuery($qa);
			$i->auts=$db->loadObjectList();
		}
		
		//Get Cats
		foreach ($items as &$i) {
			$qc=$db->getQuery(true);
			$qc->select('c.cat_id,c.cat_title,c.cat_alias');
			$qc->from('#__mams_artcat as ac');
			$qc->join('RIGHT','#__mams_cats AS c ON ac.ac_cat = c.cat_id');
			$qc->where('ac.published >= 1');
			$qc->where('c.published >= 1');
			$qc->where('c.access IN ('.implode(",",$alvls).')');
			$qc->where('ac.ac_art = '.$i->art_id);
			$qc->order('ac.ordering ASC');
			$db->setQuery($qc);
			$i->cats=$db->loadObjectList();
		}
		
		$output = "";
		$output .=  '<ul>';
		foreach ($items as $a) {
			$output .= '<li>';
			$output .= '<span class="mams-catplug-title">';
			$output .= '<a href="'.JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.":".$a->art_alias).'">';
			$output .= $a->art_title;
			$output .= '</a></span>';
			/*if ($a->auts) {
				$auts = Array();
				foreach ($a->auts as $f) {
					if ($params->get('link_pubinfo',0)) $auts[]='<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$f->auth_sec."&autid=".$f->auth_id.":".$f->auth_alias).'" class="mams-artlist-autlink">'.$f->auth_name.'</a>';
					else $auts[]=$f->auth_name;
				}
				$output .= '<br /><span class="mams-featmod-author">';
				$output .= implode(", ",$auts);
				$output .= '</span>';
			}
			if ($params->get('show_pubinfo',1)) {
				//Section Link
				$output .= '<br />';
				if ($params->get('show_pubsec',1)) {
					
					$output .= '<span class="mams-featmod-sec">';
					if ($params->get('link_pubinfo',0)) $output .= '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$a->sec_id.":".$a->sec_alias).'" class="mams-artlist-seclink">';
					$output .= '<em>'.$a->sec_name.'</em>';
					if ($params->get('link_pubinfo',0)) $output .= '</a>';
					$output .= '</span>';
				}
				
				//Pub Date
				if ($params->get('show_pubdate',1)) {
					$output .= '<span class="mams-featmod-pubdate">';
					$output .= ' published on <strong>';
					$output .= date("F j, Y",strtotime($a->art_published));
					$output .= '</strong>';
					$output .= '</span>';
				}
				
				//Cat Links
				if ($a->cats && $params->get('show_pubcat',1)) {
					$output .= '<span class="mams-featmod-cat">';
					if ($params->get('show_pubdate',1)) {
						$output .= ' in <em>';
					} else {
						$output .= ' - <em>';
					}
					$cats = Array();
					foreach ($a->cats as $c) {
						if ($params->get('link_pubinfo',0)) $cats[]='<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=category&secid=".$a->sec_id.":".$a->sec_alias."&catid=".$c->cat_id.":".$c->cat_alias).'" class="mams-artlist-catlink">'.$c->cat_title.'</a>';
						else $cats[]=$c->cat_title;
					}
					$output .= implode(", ",$cats);
					$output .= '</em>';
					$output .= '</span>';
				}
			}*/
			$output .= '</li>';
		}
		$output .= '</ul>';
		return $output;
	}
}




?>
