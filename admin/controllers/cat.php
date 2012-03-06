<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: cat.php 2012-03-06 $
 * @package		MAMS.Admin
 * @subpackage	cat
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * MAMS Category Edit Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	cat
 * @since		1.0
 */
class MAMSControllerCat extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_Cat";
}
