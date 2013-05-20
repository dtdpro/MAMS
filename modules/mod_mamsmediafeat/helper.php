<?php


// no direct access
defined('_JEXEC') or die;

class modMAMSMediaFeatHelper
{
	static function getFeatured()
	{
		$db		= JFactory::getDbo();
		$user = JFactory::getUser();
		$cfg = MAMSHelper::getConfig();
		
		$alvls = Array();
		$alvls = $user->getAuthorisedViewLevels();
		$alvls = array_merge($alvls,$cfg->reggroup);
		
		$query	= $db->getQuery(true);

		$query->select('f.*,m.*');
		$query->from('#__mams_mediafeat as f');
		$query->join('LEFT', '#__mams_media AS m ON m.med_id = f.mf_media');
		$query->where('m.feataccess IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
		$query->where('m.published >= 1');
		$query->order('f.ordering ASC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		
		return $items;
	}

}
