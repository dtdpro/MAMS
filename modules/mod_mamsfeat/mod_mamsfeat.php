<?php

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
require_once('components/com_mams/helpers/mams.php');
require_once('components/com_mams/router.php');

$articles	= modMAMSFeatHelper::getFeatured();

require JModuleHelper::getLayoutPath('mod_mamsfeat', $params->get('layout', 'default'));
