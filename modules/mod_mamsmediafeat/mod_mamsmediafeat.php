<?php

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
require_once('components/com_mams/helpers/mams.php');
require_once('components/com_mams/router.php');


$doc = JFactory::getDocument();
//jQuery
JHtml::_('jquery.framework');
$doc->addScript('media/com_mams/mediaelementjs/mediaelement-and-player.js');
$doc->addStyleSheet('media/com_mams/mediaelementjs/mediaelementplayer.css');

$items	= modMAMSMediaFeatHelper::getFeatured();

require JModuleHelper::getLayoutPath('mod_mamsmediafeat', $params->get('layout', 'default'));
