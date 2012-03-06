<?php
// no direct access
defined('_JEXEC') or die;
/**
 * @version		$Id: mams.php 2012-03-05 $
 * @package		MAMS.Admin
 * @subpackage	Entry
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_melo')) {
return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$document = JFactory::getDocument();
$document->addStyleDeclaration('.icon-48-mams {background-image: url(../media/com_mams/images/mams-48x48.png);}');

$controller = JController::getInstance('mams');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

