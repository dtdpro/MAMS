<?php

use YooTheme\Database;

class MAMSProvider
{
    public static function getCats($artcount = false, $parent=0,$orderby="titasc",$onlyFeatCat=false,$restrictFeatCat=false) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$query->select('c.*');
		$query->from('#__mams_cats AS c');
		if (is_array($parent)) {
			$query->where( 'c.parent_id IN (' . implode(",",$parent).')' );
		} else {
			$query->where( 'c.parent_id = ' . (int) $parent );
		}
		$query->where('c.published >= 1');
		$query->where('c.access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		switch ($orderby) {
			case "titasc": $query->order('cat_title ASC'); break;
			case "titdsc": $query->order('cat_title DESC'); break;
            case "orderasc": $query->order('ordering ASC'); break;
            case "orderdsc": $query->order('ordering ASC'); break;
			default: $query->order('cat_title ASC'); break;
		}
		if ($onlyFeatCat) {
			$query->where('c.cat_featured = 1');
		}
		$db->setQuery($query);
		$items = $db->loadObjectList();

		// Remove categories not in Access Level List when enabled
		if ($onlyFeatCat || $restrictFeatCat) {
			foreach ($items as $k => $i) {
				$alintersect = array_intersect(explode(",",$i->cat_feataccess),$user->getAuthorisedViewLevels());
				if (!count($alintersect)) {
					unset($items[$k]);
				}
			}
		}
		
		if ($artcount) {
			foreach ($items as &$i) {
				$query = $db->getQuery(true);
				$query->select('ac.ac_art');
				$query->from('#__mams_artcat AS ac');
				$query->where('ac.ac_cat = '.$i->cat_id);
				$query->where('ac.published >= 1');
				$db->setQuery($query);
				$arts = $db->loadColumn();
				$i->cat_title = $i->cat_title.' ('.count($arts).')';
			}
		}
		
		return $items;
	}
}
