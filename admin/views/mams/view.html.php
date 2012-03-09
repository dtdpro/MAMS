<?php
defined('_JEXEC') or die();
/**
 * @version		$Id: view.html.php 2012-03.05 $
 * @package		MAMS.Admin
 * @subpackage	mams
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */



jimport( 'joomla.application.component.view' );

/**
 * MAMS MAMS View
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	mams
 * @since		1.0
 */

class MAMSViewMAMS extends JView
{
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'MAMS Article Management System' ), 'mams' );
		JToolBarHelper::preferences('com_mams');
		parent::display($tpl);
	}
}
