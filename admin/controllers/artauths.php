<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artauths.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artauths
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * MAMS Article Authors Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artauths
 * @since		1.0
 */
class MAMSControllerArtAuths extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_ARTAUTH";
	
	public function getModel($name = 'ArtAuth', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}