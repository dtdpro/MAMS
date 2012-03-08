<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: medias.php 2012-03-07 $
 * @package		MAMS.Admin
 * @subpackage	medias
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * MAMS Media Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	media
 * @since		1.0
 */
class MAMSControllerMedias extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_MEDIA";
	
	public function getModel($name = 'Media', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}