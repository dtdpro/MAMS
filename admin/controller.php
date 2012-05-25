<?php
defined('_JEXEC') or die();
/**
 * @version		$Id: controller.php 2012-03-05 $
 * @package		MAMS.Admin
 * @subpackage	controller
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

jimport('joomla.application.component.controller');

/**
 * MAMS Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	mams
 * @since		1.0
 */
class MAMSController extends JController
{
	protected $default_view = 'mams';
	
	function display()
	{
		require_once JPATH_COMPONENT.'/helpers/mams.php';
		// Set the submenu
		MAMSHelper::addSubmenu(JRequest::getVar('view'),JRequest::getCmd('extension', 'com_mams'));
		parent::display();
		return $this;
	}
}
