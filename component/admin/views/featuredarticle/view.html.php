<?php

defined('_JEXEC') or die;

class MAMSViewFeaturedarticle extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		// Set the submenu
		MAMSHelper::addSubmenu(JRequest::getVar('view'),JRequest::getCmd('extension', 'com_mams'));

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$state	= $this->get('State');

		JToolBarHelper::title(JText::_('COM_MAMS_FEATUREDARTICLE_TITLE'), 'mams');
		JToolbarHelper::custom('featuredarticle.delete', 'remove.png', 'remove_f2.png', 'JTOOLBAR_REMOVE', true);


	}
	
	protected function getSortFields()
	{
		return array(
				'f.ordering' => JText::_('JGRID_HEADING_ORDERING'),
				'a.art_title' => JText::_('JGLOBAL_TITLE'),
				'a.art_id' => JText::_('JGRID_HEADING_ID')
		);
	}
}