<?php
/*
MAMS DownLoader
(C) 2011 DtD Productions  http://dtdpro.com
dl.php - External downloader
*/
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__) . '/../..' );
define('JPATH_CORE', JPATH_BASE . '/../..');
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once(JPATH_BASE.DS.'components/com_mams/helpers'.DS.'mams.php');
jimport('joomla.filesystem.file');

$mainframe =& JFactory::getApplication('site');
$session =& JFactory::getSession();
$cfg =& JFactory::getConfig();
$db  =& JFactory::getDBO();
$user =& JFactory::getUser();

$dlid=JRequest::getVar('dlid');


$q='SELECT * FROM #__mams_dloads WHERE  published = 1 && access IN ('.implode(",",$user->getAuthorisedViewLevels()).') && dl_id = '.$dlid;
$db->setQuery($q);
$r=$db->loadObject();

if ($r->dl_id) {
	MAMSHelper::trackViewed($dlid,'dload');
	JResponse::clearHeaders();
	JResponse::setHeader("Pragma","public"); 
	JResponse::setHeader('Cache-Control', 'no-cache, must-revalidate', true);
	JResponse::setHeader('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT', true);
	switch ($r->dl_type) {
		case 'mp3': JResponse::setHeader('Content-Type', 'audio/mpeg', true); break;
		case 'pdf': JResponse::setHeader('Content-Type', 'application/pdf', true); break;
	}
	$filename = JFile::getName($r->dl_fname);
	//JResponse::setHeader("Cache-Control","private",false);
	JResponse::setHeader('Content-Description', 'File Transfer', true);
	JResponse::setHeader('Content-Disposition', 'attachment; filename="'.$filename.'"', true);
	JResponse::setHeader('Content-Transfer-Encoding', 'binary', true);
	//JResponse::setHeader("Content-Length: ".filesize(JPATH_BASE.'/'.$r->dl_loc));
	//JResponse::setHeader('Location', $r->dl_loc, true);
	JResponse::sendHeaders();
	readfile(JPATH_BASE.'/'.$r->dl_loc.$r->dl_fname);
} else {
	$url=JRoute::_("index.php");
	$mainframe->redirect($url, JText::_('Access Denied') );
}
exit();
?>

