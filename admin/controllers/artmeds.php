<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artmeds.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artmeds
 * @copyright	Copyright (C) 2012 DtD Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * MAMS Article Media Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artmeds
 * @since		1.0
 */
class MAMSControllerArtMeds extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_ARTMED";
	
	public function getModel($name = 'ArtMed', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}