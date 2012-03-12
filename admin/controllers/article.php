<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: auth.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	article
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * MAMS Article Edit Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	article
 * @since		1.0
 */
class MAMSControllerArticle extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_ARTICLE";
}
