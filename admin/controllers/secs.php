<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: secs.php 2012-03.05 $
 * @package		MAMS.Admin
 * @subpackage	secs
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * MAMS Sections Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	secs
 * @since		1.0
 */
class MAMSControllerSecs extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_SEC";
	
	public function getModel($name = 'Sec', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}