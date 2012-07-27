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
 * MAMS Stats View
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	mams
 * @since		1.0
 */

class MAMSViewStats extends JView
{
	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'MAMS Stats' ), 'mams' );
		$model = $this->getModel();
		$tbar =& JToolBar::getInstance('toolbar');
		$tbar->appendButton('Link','archive','Export CSV','index.php?option=com_mams&view=stats&format=csv" target="_blank');
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->startdate = $model->getState('startdate');
		$this->enddate = $model->getState('enddate');
		$this->filter_type = $model->getState('filter_type');
		$this->filter_group = $model->getState('filter.group');
		
		$this->config=MAMSHelper::getConfig();
		if ($this->config->continued || $this->config->mue) {
			$this->grouplist=$this->get('UserGroups');
		}
		
		parent::display($tpl);
	}
}
