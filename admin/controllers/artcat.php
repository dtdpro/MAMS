<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artcat.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artcat
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * MAMS Article Category Edit Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artcat
 * @since		1.0
 */
class MAMSControllerArtCat extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_ARTCAT";
}
