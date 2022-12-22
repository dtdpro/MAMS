<?php

defined('_JEXEC') or die;

class MAMSViewFeaturedmedia extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;

		$this->state		= $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		if (JVersion::MAJOR_VERSION == 3)  MAMSHelper::addSubmenu($jinput->getVar('view'),$jinput->getVar('extension', 'com_mams'));

		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->addToolBar();
		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$state	= $this->get('State');

		JToolBarHelper::title(JText::_('COM_MAMS_FEATUREDMEDIA_TITLE'), 'mams');
		JToolbarHelper::custom('featuredmedia.delete', 'remove.png', 'remove_f2.png', 'JTOOLBAR_REMOVE', true);


	}
	
	protected function getSortFields()
	{
		return array(
				'f.ordering' => JText::_('JGRID_HEADING_ORDERING'),
				'm.med_inttitle' => JText::_('JGLOBAL_TITLE'),
				'm.med_id' => JText::_('JGRID_HEADING_ID')
		);
	}
}