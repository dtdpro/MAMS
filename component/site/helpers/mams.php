<?php
/**
 * @version		$Id: mams.php 2012-01.24 $
 * @package		MAMS.Site
 * @subpackage	com_mams
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */
defined('_JEXEC') or die('Restricted access');

/**
 * MAMS Component Helper
 *
 * @static
 * @package		MAMS.Site
 * @subpackage	com_mams
 * @since		1.00
 */
class MAMSHelper {

	/**
	* Get configuration for component.
	*
	* @return object The current config parameters
	*
	* @since 1.00
	*/
	function getConfig() {
		$menuConfig = JComponentHelper::getParams('com_mams'); 
		$mamscfg = $menuConfig->toObject();
		return $mamscfg;
	}
	
	/**
	* Track item Viewed
	*
	* @param int $item Item id number
	* @param string $type Item type.
	*
	* @return boolean true if scucessfull, false if not.
	*
	* @since 1.0
	*/
	function trackViewed($item, $type) {
		$db =& JFactory::getDBO();
		$sewn = JFactory::getSession();
		$sessionid = $sewn->getId();
		$user =& JFactory::getUser();
		$userid = $user->id;
		$q = $db->getQuery(true);
		$q->insert('#__mams_track');
		$q->columns(array($db->quoteName('mt_item'),$db->quoteName('mt_type'),$db->quoteName('mt_user'),$db->quoteName('mt_session'),$db->quoteName('mt_ipaddr')));
		$q->values('"'.$item.'","'.$type.'","'.$userid.'","'.$sessionid.'","'.$_SERVER['REMOTE_ADDR'].'"');
		$db->setQuery($q); 
		if ($db->query()) return 1;
		else return 0;
	}
	
	
}
