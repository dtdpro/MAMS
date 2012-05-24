<?php
/**
 * MAMS entry point file for MAMS Component
 * (C) 2012 Corona Productions
 * Coded and Created by: Mike Amundsen
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
$doc->addStyleSheet('media/com_mams/templates/'.$cfg->template.'/mams.css');
$doc->addScript('media/com_mams/vidplyr/jwplayer.js');

// Create the controller
$classname	= 'MAMSController'.$controller;
$controller = new $classname( );
//JPluginHelper::importPlugin('continued');

// Perform the Request task
$controller->execute( JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();
?>

