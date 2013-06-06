<?php

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
require_once('components'.DS.'com_mams'.DS.'helpers'.DS.'mams.php');
require_once('components'.DS.'com_mams'.DS.'router.php');


$doc = &JFactory::getDocument();
//jQuery
if (!JFactory::getApplication()->get('jquery')) {
	JFactory::getApplication()->set('jquery', true);
	// add jQuery
	$doc->addScript('media/com_mams/scripts/jquery.js');	
}
$doc->addScript('media/com_mams/mediaelementjs/mediaelement-and-player.js');
$doc->addStyleSheet('media/com_mams/mediaelementjs/mediaelementplayer.css');

$items	= modMAMSMediaFeatHelper::getFeatured();

require JModuleHelper::getLayoutPath('mod_mamsmediafeat', $params->get('layout', 'default'));
