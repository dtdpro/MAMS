<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artmed.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artmed
 * @copyright	Copyright (C) 2012 DtD Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * MAMS Article Media Edit Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artmed
 * @since		1.0
 */
class MAMSControllerArtMed extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_ARTMED";
}
