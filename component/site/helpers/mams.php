<?php
defined('_JEXEC') or die('Restricted access');

class MAMSHelper {

	public static function getConfig() {
		$menuConfig = JComponentHelper::getParams('com_mams'); 
		$mamscfg = $menuConfig->toObject();
		return $mamscfg;
	}

	public static function trackViewed($item, $type) {
		$db = JFactory::getDBO();
		$sewn = JFactory::getSession();
		$sessionid = $sewn->getId();
		$user = JFactory::getUser();
		$userid = $user->id;
		$q = $db->getQuery(true);
		$q->insert('#__mams_track');
		$q->columns(array($db->quoteName('mt_item'),$db->quoteName('mt_type'),$db->quoteName('mt_user'),$db->quoteName('mt_session'),$db->quoteName('mt_ipaddr')));
		$q->values('"'.$item.'","'.$type.'","'.$userid.'","'.$sessionid.'","'.$_SERVER['REMOTE_ADDR'].'"');
		$db->setQuery($q); 
		if ($db->execute()) return $db->insertid();
		else return 0;
	}
	
	
}
