<?php

/**
 * @package   MAMS for YOOtheme Pro
 * @author    DtD Productions
 * @copyright Copyright (C) 2024 DtD Productions
 * @license   GNU General Public License version 2, see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use YOOtheme\Application;

class plgSystemMAMSYT extends CMSPlugin
{
    public function onAfterInitialise()
    {
        // Check if YOOtheme Pro is loaded
        if (!class_exists(Application::class, false)) {
            return;
        }

        // Load all modules
        $app = Application::getInstance();
        $app->load(__DIR__ . '/modules/*/bootstrap.php');
    }
}