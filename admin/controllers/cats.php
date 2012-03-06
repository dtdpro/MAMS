<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: cats.php 2012-03-06 $
 * @package		MAMS.Admin
 * @subpackage	cats
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * MAMS Categories Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	cats
 * @since		1.0
 */
class MAMSControllerCats extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_CAT";
	
	public function getModel($name = 'Cat', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}