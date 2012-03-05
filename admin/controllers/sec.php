<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: sec.php 2012-03.05 $
 * @package		MAMS.Admin
 * @subpackage	sec
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * MAMS Section Edit Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	sec
 * @since		1.0
 */
class MAMSControllerSec extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_SEC";
}
