<?php
/**
 * MAMS entry point file for MAMS Component
 * (C) 2013-2014 DtD Productions
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once (JPATH_COMPONENT.'/controller.php');

// Require specific controller if requested
if($controller = JFactory::getApplication()->input->get('controller')) {
	require_once (JPATH_COMPONENT.'/controllers/'.$controller.'.php');
}

// Load helper
require_once(JPATH_COMPONENT.'/helpers/mams.php');

// Load StyleSheet for template, based on config
$cfg = MAMSHelper::getConfig();
$doc = JFactory::getDocument();

//jQuery
JHtml::_('jquery.framework');
$doc->addScript('media/com_mams/mediaelementjs/mediaelement-and-player.js');
$doc->addScript('media/com_mams/scripts/mams.js');
$doc->addScriptDeclaration("var mamsuri = '".JURI::base( true )."';");
if (JFactory::getApplication()->input->get('format') != "feed") {
	$doc->addStyleSheet('media/com_mams/mediaelementjs/mediaelementplayer.css');
	if ($cfg->compcss) $doc->addStyleSheet('components/com_mams/mams.css');
}

// Create the controller
$classname	= 'MAMSController'.$controller;
$controller = new $classname( );
JPluginHelper::importPlugin('mams');

// Perform the Request task
$controller->execute( JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
?>

