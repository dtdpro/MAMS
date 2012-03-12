<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artauth.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artauth
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * MAMS Article Author Edit Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artauth
 * @since		1.0
 */
class MAMSControllerArtAuth extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_ARTAUTH";
}
