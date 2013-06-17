<?php
// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_mams')) {
return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$document = JFactory::getDocument();
$document->addStyleDeclaration('.icon-48-mams {background-image: url(../media/com_mams/images/mams-48x48.png);}');

$controller = JControllerLegacy::getInstance('mams');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

