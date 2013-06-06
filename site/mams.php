<?php
/**
 * MAMS entry point file for MAMS Component
 * (C) 2013 DtD Productions
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

// Require specific controller if requested
if($controller = JRequest::getVar('controller')) {
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
}

// Load helper
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'mams.php');

// Load StyleSheet for template, based on config
$cfg = MAMSHelper::getConfig();
$doc = &JFactory::getDocument();
//jQuery
if (!JFactory::getApplication()->get('jquery')) {
	JFactory::getApplication()->set('jquery', true);
	// add jQuery
	$doc->addScript('media/com_mams/scripts/jquery.js');	
}
//$doc->addScript('media/com_mams/vidplyr/jwplayer.js');
//$doc->addScript('media/com_mams/scripts/mams.js');
//$doc->addScriptDeclaration("var mamsuri = '".JURI::base( true )."';");
$doc->addScript('media/com_mams/mediaelementjs/mediaelement-and-player.js');
$doc->addStyleSheet('media/com_mams/mediaelementjs/mediaelementplayer.css');

// Create the controller
$classname	= 'MAMSController'.$controller;
$controller = new $classname( );
JPluginHelper::importPlugin('mams');

// Perform the Request task
$controller->execute( JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();
?>

