<?php

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
require_once('components/com_mams/helpers/mams.php');
require_once('components/com_mams/router.php');

$articles	= modMAMSTagHelper::getFeatured($params);
$taginfo = modMAMSTagHelper::getTagInfo($params);
require JModuleHelper::getLayoutPath('mod_mamstag', $params->get('layout', 'default'));
