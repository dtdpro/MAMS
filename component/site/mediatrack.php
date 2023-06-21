<?php
// Set flag that this is a parent file
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__) . '/../..' );

require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
if (JVersion::MAJOR_VERSION == 3) {
	$app = JFactory::getApplication( 'site' );
} else {
	//$startTime = microtime(1);
	//$startMem  = memory_get_usage();
	if (!file_exists(JPATH_LIBRARIES . '/vendor/autoload.php') || !is_dir(JPATH_ROOT . '/media/vendor'))
	{
		echo file_get_contents(JPATH_ROOT . '/templates/system/build_incomplete.html');

		exit;
	}

	// Set profiler start time and memory usage and mark afterLoad in the profiler.
	//JDEBUG && \Joomla\CMS\Profiler\Profiler::getInstance('Application')->setStart($startTime, $startMem)->mark('afterLoad');

	// Boot the DI container
	$container = \Joomla\CMS\Factory::getContainer();

	$container->alias('session.web', 'session.web.site')
	          ->alias('session', 'session.web.site')
	          ->alias('JSession', 'session.web.site')
	          ->alias(\Joomla\CMS\Session\Session::class, 'session.web.site')
	          ->alias(\Joomla\Session\Session::class, 'session.web.site')
	          ->alias(\Joomla\Session\SessionInterface::class, 'session.web.site');

	// Instantiate the application.
	$app = $container->get(\Joomla\CMS\Application\SiteApplication::class);

	// Set the application as global app
	\Joomla\CMS\Factory::$application = $app;
}


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



