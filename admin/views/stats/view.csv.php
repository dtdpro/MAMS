<?php
defined('_JEXEC') or die();
/**
 * @version		$Id: view.html.php 2012-03.05 $
 * @package		MAMS.Admin
 * @subpackage	stats
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */



jimport( 'joomla.application.component.view' );

/**
 * MAMS Stats View CSV
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	mams
 * @since		1.0
 */

class MAMSViewStats extends JView
{
	function display($tpl = 'csv')
	{
		$model = $this->getModel();
		$this->items = $model->getItemsCSV();
		$this->config=MAMSHelper::getConfig();
		parent::display($tpl);
	}
}
