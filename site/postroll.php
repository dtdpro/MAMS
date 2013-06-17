<?php
define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(__FILE__) . '/../..' );
define('JPATH_CORE', JPATH_BASE . '/../..');
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
jimport('joomla.filesystem.file');

$mainframe =& JFactory::getApplication('site');
$session =& JFactory::getSession();
$cfg =& JFactory::getConfig();
$db  =& JFactory::getDBO();
$user =& JFactory::getUser();

$medid=JRequest::getVar('medid');

$q='SELECT med_postroll FROM #__mams_media WHERE published = 1 && access IN ('.implode(",",$user->getAuthorisedViewLevels()).') && med_id = '.$medid;
$db->setQuery($q);

echo '<div class="mams-mod-featmedia-player-postroll">';
echo $db->loadResult();
echo '</div>';
?>

