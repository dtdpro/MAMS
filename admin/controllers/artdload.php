<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artdload.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artdload
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * MAMS Article Download Edit Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artdload
 * @since		1.0
 */
class MAMSControllerArtDload extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_ARTDLOAD";
}
