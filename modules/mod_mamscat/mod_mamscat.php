<?php

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
require_once('components'.DS.'com_mams'.DS.'helpers'.DS.'mams.php');
require_once('components'.DS.'com_mams'.DS.'router.php');

$articles	= modMAMSCatHelper::getFeatured($params);

require JModuleHelper::getLayoutPath('mod_mamscat', $params->get('layout', 'default'));
