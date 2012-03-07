<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: auth.php 2012-03-07 $
 * @package		MAMS.Admin
 * @subpackage	auth
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * MAMS Author Edit Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	auth
 * @since		1.0
 */
class MAMSControllerAuth extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_AUTH";
}
