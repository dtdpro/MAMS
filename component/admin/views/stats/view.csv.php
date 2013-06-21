<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class MAMSViewStats extends JViewLegacy
{
	function display($tpl = 'csv')
	{
		$model = $this->getModel();
		$this->items = $model->getItemsCSV();
		$this->config=MAMSHelper::getConfig();
		parent::display($tpl);
	}
}
