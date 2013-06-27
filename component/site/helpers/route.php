<?php
defined('_JEXEC') or die;

abstract class MAMSHelperRoute
{
	public static function getArticleRoute($artid, $secid = 0, $language = 0)
	{
		//Create the link
		$link = 'index.php?option=com_mams&view=article&artid='. $artid.'&secid='.$secid;

		return $link;
	}
	public static function getAuthorRoute($authid, $secid = 0, $language = 0)
	{
		//Create the link
		$link = 'index.php?option=com_mams&view=author&autid='. $authid.'&secid='.$secid;

		return $link;
	}
	public static function getSectionRoute($secid, $cat=0, $language = 0)
	{
		//Create the link
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('sec_type')->from("#__mams_secs")->where("sec_id = ".(int)$secid);
		$db->setQuery($query);
		$sectype=$db->loadResult();
		if ($sectype == "article") $link = 'index.php?option=com_mams&view=artlist&secid='. $secid;
		if ($sectype == "author") $link = 'index.php?option=com_mams&view=author&layout=list&secid='. (int)$secid;

		return $link;
	}

}
