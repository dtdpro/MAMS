<?php
// Set flag that this is a parent file
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__) . '/../..' );

require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

$app = JFactory::getApplication('site');
$db  = JFactory::getDBO();
$user = JFactory::getUser();



$item_id = urldecode($app->input->getInt('item_id'));
$track_id = urldecode($app->input->getInt('track_id'));
$secs_played  = urldecode($app->input->getInt('secs_played'));
$per_played  = urldecode($app->input->get('per_played'));
$userid = $user->id;
$session = JFactory::getSession();


$q = $db->getQuery(true);
$q->insert('#__mams_track');
$q->columns(array($db->quoteName('mt_item'),$db->quoteName('mt_type'),$db->quoteName('mt_user'),$db->quoteName('mt_session'),$db->quoteName('mt_ipaddr')));
$q->values('"'.$item_id.'","media","'.$userid.'","'.$session->getId().'","'.$_SERVER['REMOTE_ADDR'].'"');
$db->setQuery($q);
$db->query();



