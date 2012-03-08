<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: dloads.php 2012-03-07 $
 * @package		MAMS.Admin
 * @subpackage	dloads
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * MAMS Downloads Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	dload
 * @since		1.0
 */
class MAMSControllerDloads extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_DLOAD";
	
	public function getModel($name = 'Dload', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}