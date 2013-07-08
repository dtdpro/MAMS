<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');



class MAMSControllerArticles extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_ARTICLE";
	
	public function getModel($name = 'Article', $prefix = 'MAMSModel', $config = array('ignore_request' => true)) 
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	function featured()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	
		// Get items to remove from the request.
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
	
		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.featured', 'com_mams.article.'.(int) $id))
			{
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}
		
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
	
		// Get items to remove from the request.
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
	
		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.featured', 'com_mams.article.'.(int) $id))
			{
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}
		
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
	
	public function saveOrderAjax()
	{
		// Get the input
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');
	
		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);
	
		// Get the model
		$model = $this->getModel();
	
		// Save the ordering
		$return = $model->saveorder($pks, $order);
	
		if ($return)
		{
			echo "1";
		}
	
		// Close the application
		JFactory::getApplication()->close();
	}
	
	function drilldowns()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$context = "com_mams.drilldowns";
	
		// Get items to remove from the request.
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
	
		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.featured', 'com_mams.article.'.(int) $id))
			{
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}
		
		if (!is_array($ids) || count($ids) < 1)
		{
			JError::raiseWarning(500, JText::_('COM_MCME_PAGE_NO_ITEM_SELECTED'));
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=pages', false));
		}
		else
		{
			$app->setUserState($context . '.filter.article',$ids[0]);
		}
	
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=artauths', false));
	}
}