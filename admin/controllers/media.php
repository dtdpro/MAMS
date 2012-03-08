<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: media.php 2012-03-08 $
 * @package		MAMS.Admin
 * @subpackage	media
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * MAMS Media Edit Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	media
 * @since		1.0
 */
class MAMSControllerMedia extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_MEDIA";
}
