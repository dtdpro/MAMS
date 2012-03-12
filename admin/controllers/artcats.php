<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artcats.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	auths
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * MAMS Article Categories Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artcats
 * @since		1.0
 */
class MAMSControllerArtCats extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_ARTAUTH";
	
	public function getModel($name = 'ArtCat', $prefix = 'MAMSModel') 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}