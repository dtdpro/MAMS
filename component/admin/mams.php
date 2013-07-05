<?php
// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_mams')) {
return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_COMPONENT.'/helpers/mams.php';

$controller = JControllerLegacy::getInstance('mams');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

