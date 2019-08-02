<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class MAMSControllerArticle extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_ARTICLE";
	
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();
		$secId = JArrayHelper::getValue($data, 'art_sec', $this->input->getInt('filter_sec'), 'int');
		$allow = null;
	
		if ($secId)
		{
			// If the section has been passed in the data or URL check it.
			$allow = $user->authorise('core.create', 'com_mams.sec.' . $secId);
		} else {
			$allow = count(MAMSHelper::getAuthorisedSecs('core.create'));
		}
	
		if ($allow === null)
		{
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}
		else
		{
			return $allow;
		}
	}
	
	protected function allowEdit($data = array(), $key = 'art_id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();
		$userId = $user->get('id');
	
		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_mams.article.' . $recordId))
		{
			return true;
		}
	
		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_mams.article.' . $recordId))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['art_added_by']) ? $data['art_added_by'] : 0;
			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);
	
				if (empty($record))
				{
					return false;
				}
	
				$ownerId = $record->art_added_by;
			}
	
			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}
	
		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}
	
	public function batch($model = null)
	{
		$this->checkToken();

		// Set the model
		$model = $this->getModel('Article', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_mams&view=articles' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
}
