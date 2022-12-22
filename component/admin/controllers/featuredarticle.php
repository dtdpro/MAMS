<?php
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

require_once dirname(__FILE__).'/articles.php';

class MAMSControllerFeaturedarticle extends MAMSControllerArticles
{
	

	public function getModel($name = 'Featurearticle', $prefix = 'MAMSModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		$ids = $this->input->get('cid', array(), 'array');
	
		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
	
			// Remove the items.
			if (!$model->defeatured($ids))
			{
				JError::raiseWarning(500, $model->getError());
			}
		}
	
		$this->setRedirect('index.php?option=com_mams&view=featuredarticle');
	}
	
	public function saveOrderAjax()
	{
		// Get the input
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');
	
		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);
	
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
}