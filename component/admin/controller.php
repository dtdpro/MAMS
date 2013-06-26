<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class MAMSController extends JControllerLegacy
{
	protected $default_view = 'mams';
	
	function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/mams.php';
		
		$view = $this->input->get('view', 'articles');
		$layout = $this->input->get('layout', 'articles');
		$art_id = $this->input->getInt('art_id');
		
		// Check for edit form.
		if ($view == 'article' && $layout == 'edit' && !$this->checkEditId('com_mams.edit.article', $art_id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_mams&view=articles', false));
		
			return false;
		}
		
		parent::display();
		return $this;
	}
}
