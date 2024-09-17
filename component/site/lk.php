<?php
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__) . '/../..' );
define('JPATH_CORE', JPATH_BASE . '/../..');

require_once ( JPATH_BASE.'/includes/defines.php' );
require_once ( JPATH_BASE.'/includes/framework.php' );
require_once(JPATH_BASE.'/components/com_mams/helpers/mams.php');
jimport('joomla.filesystem.file');

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

$session = JFactory::getSession();
$cfg = JFactory::getConfig();
$db  = JFactory::getDBO();
$user = JFactory::getUser();

$dlid=$app->input->getInt('linkid',0);

$q=$db->getQuery(true);
$q->select('*');
$q->from('#__mams_links');
$q->where('link_id = '.$dlid);
$q->where('published >= 1');
$q->where('access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
$db->setQuery($q);
$r=$db->loadObject();

if ($r->link_id) {
	MAMSHelper::trackViewed($dlid,'link');
	header("Location: ".$r->link_url);
	$app->close();
} else {
	http_response_code(404);
}
exit();
?>

