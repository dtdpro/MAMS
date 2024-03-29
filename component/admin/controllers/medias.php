<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

class MAMSControllerMedias extends JControllerAdmin
{

	protected $text_prefix = "COM_MAMS_MEDIA";
	
	public function getModel($name = 'Media', $prefix = 'MAMSModel', $config = [])
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	function featured()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	
		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
	
		if (!is_array($cid) || count($cid) < 1)
		{
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
	
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			ArrayHelper::toInteger($cid);
	
			// Remove the items.
			if ($model->featured($cid,1))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_FEATURED', count($cid)));
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
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
	
		if (!is_array($cid) || count($cid) < 1)
		{
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
	
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			ArrayHelper::toInteger($cid);
	
			// Remove the items.
			if ($model->featured($cid,0))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DEFEATURED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
	
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
}