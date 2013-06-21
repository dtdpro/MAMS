<?php
// Set flag that this is a parent file
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__) . '/../..' );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$mainframe =& JFactory::getApplication('site');
$db  =& JFactory::getDBO();
$user = &JFactory::getUser();



$item_id = urldecode(JRequest::getVar('item_id'));
$track_id = urldecode(JRequest::getVar('track_id'));
$secs_played  = urldecode(JRequest::getVar('secs_played'));
$per_played  = urldecode(JRequest::getVar('per_played'));
$userid = $user->id;
$session =& JFactory::getSession();

if (!$track_id) { 
	$qc = 'INSERT INTO #__mams_mediatrack (mt_user,mt_item,mt_seconds,mt_percentage,mt_session,mt_ipaddr) ';
	$qc .= 'VALUES ('.$userid.',"'.$item_id.'","'.$secs_played.'","'.$per_played.'","'.$session->getId().'","'.$_SERVER['REMOTE_ADDR'].'")';
	$db->setQuery( $qc );
	$db->query();
	echo $db->insertid();
} else {
	$qc = 'UPDATE #__mams_mediatrack SET mt_seconds = "'.$secs_played.'", mt_percentage = "'.$per_played.'" ';
	$qc .= ' WHERE mt_id = "'.$track_id.'"';
	$db->setQuery( $qc );
	$db->query();
	
	
}





