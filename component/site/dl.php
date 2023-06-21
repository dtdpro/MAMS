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

$dlid=$app->input->getInt('dlid',0);

$q=$db->getQuery(true);
$q->select('*');
$q->from('#__mams_dloads');
$q->where('dl_id = '.$dlid);
$q->where('published >= 1');
$q->where('access IN ('.implode(",",$user->getAuthorisedViewLevels()).')');
$db->setQuery($q);
$r=$db->loadObject();

if ($r->dl_id) {
	MAMSHelper::trackViewed($dlid,'dload');
	$filename = basename($r->dl_fname);
	switch ($r->dl_type) {
		case 'mp3': $contentType =  'audio/mpeg'; break;
		case 'pdf': $contentType = 'application/pdf'; break;
		case 'zip': $contentType = 'application/zip'; break;
	}
	$app = JFactory::getApplication();
	$app->clearHeaders();
	$app->setHeader( "Pragma", "public" );
	$app->setHeader( 'Cache-Control', 'no-cache, must-revalidate', true );
	$app->setHeader( 'Expires', 'Sat, 26 Jul 1997 05:00:00 GMT', true );
	$app->setHeader( 'Content-Type', $contentType, true );
	$app->setHeader( 'Content-Description', 'File Transfer', true );
	$app->setHeader( 'Content-Disposition', 'attachment; filename="' . $filename . '"', true );
	$app->setHeader( 'Content-Transfer-Encoding', 'binary', true );
	$app->sendHeaders();
	readfile(JPATH_BASE.'/'.$r->dl_loc.$r->dl_fname);
	$app->close();
} else {
	$url=JRoute::_("index.php");
	$app->redirect($url, JText::_('Access Denied') );
}
exit();
?>

