<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: dload.php 2012-03-08 $
 * @package		MAMS.Admin
 * @subpackage	dload
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * MAMS Download Edit Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	dload
 * @since		1.0
 */
class MAMSControllerDload extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_DLOAD";
}
