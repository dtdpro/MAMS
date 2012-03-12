<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artdloads.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artdloads
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * MAMS Article Downloads Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artdloads
 * @since		1.0
 */
class MAMSControllerArtDloads extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_ARTDLOAD";
	
	public function getModel($name = 'ArtDload', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}