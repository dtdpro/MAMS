<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

class MAMSControllerTags extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_TAG";
	
	public function getModel($name = 'Tag', $prefix = 'MAMSModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	function featured()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		$user = JFactory::getUser();
	
		// Get items to remove from the request.
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
	
		if (!is_array($ids) || count($ids) < 1)
		{
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
				
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($ids);
				
			// Remove the items.
			if ($model->featured($ids,1))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_FEATURED', count($ids)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
	
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
	
	function unfeatured()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		$user = JFactory::getUser();
	
		// Get items to remove from the request.
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
	
		if (!is_array($ids) || count($ids) < 1)
		{
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
	
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($ids);
	
			// Remove the items.
			if ($model->featured($ids,0))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DEFEATURED', count($ids)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
	
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
}