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



$tracked_item = urldecode(JRequest::getVar('tracked_item'));
$current_item  = urldecode(JRequest::getVar('current_item'));
$current_page  = urldecode(JRequest::getVar('current_page'));
$tracked_value = urldecode(JRequest::getVar('tracked_value'));
$userid = $user->id;
$session =& JFactory::getSession();


$qc = 'INSERT INTO #__mams_mediatrack (mt_user,mt_tracked_item,mt_current_item,mt_current_page,mt_tracked_value,mt_session) ';
$qc .= 'VALUES ('.$userid.',"'.$tracked_item.'","'.$current_item.'","'.$current_page.'","'.$tracked_value.'","'.$session->getId().'")';
$db->setQuery( $qc );
$db->query();





