<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: auths.php 2012-03-06 $
 * @package		MAMS.Admin
 * @subpackage	auths
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * MAMS Authors Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	auths
 * @since		1.0
 */
class MAMSControllerAuths extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_AUTH";
	
	public function getModel($name = 'Auth', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}