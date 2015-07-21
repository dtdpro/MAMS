<?php

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
require_once('components/com_mams/helpers/mams.php');
require_once('components/com_mams/router.php');

$articles	= modMAMSMostHelper::getArticles($params);

require JModuleHelper::getLayoutPath('mod_mamsmost', $params->get('layout', 'default'));
