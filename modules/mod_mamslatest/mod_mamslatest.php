<?php

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
require_once('components/com_mams/helpers/mams.php');
require_once('components/com_mams/router.php');

$articles	= modMAMSLatestHelper::getFeatured($params);

require JModuleHelper::getLayoutPath('mod_mamslatest', $params->get('layout', 'default'));
