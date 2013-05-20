<?php

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
require_once('components'.DS.'com_mams'.DS.'helpers'.DS.'mams.php');
require_once('components'.DS.'com_mams'.DS.'router.php');


$doc = &JFactory::getDocument();
$doc->addScript('media/com_mams/vidplyr/jwplayer.js');
$doc->addScript('media/com_mams/scripts/mams.js');
$doc->addScriptDeclaration("var mamsuri = '".JURI::base( true )."';");

$items	= modMAMSMediaFeatHelper::getFeatured();

require JModuleHelper::getLayoutPath('mod_mamsmediafeat', $params->get('layout', 'default'));
