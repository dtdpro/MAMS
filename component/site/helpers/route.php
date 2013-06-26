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

}
