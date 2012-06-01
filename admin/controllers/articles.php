<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: articles.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	articles
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

/**
 * MAMS Articles Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artciles
 * @since		1.0
 */
class MAMSControllerArticles extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_ARTICLE";
	
	public function getModel($name = 'Article', $prefix = 'MAMSModel', $config = array('ignore_request' => true)) 
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}