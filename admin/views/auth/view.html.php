<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: view.html.php 2012-03-07 $
 * @package		MAMS.Admin
 * @subpackage	auth
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * MAMS Author Edit View
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	auth
 * @since		1.0
 */
class MAMSViewAuth extends JView
{
	/**
	 * display method of view
	 * @return void
	 */
	public function display($tpl = null) 
	{
		// get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');
		$script = $this->get('Script');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign the Data
		$this->form = $form;
		$this->item = $item;
		$this->script = $script;

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->auth_id == 0;
		JToolBarHelper::title($isNew ? JText::_('COM_MAMS_MANAGER_AUTH_NEW') : JText::_('COM_MAMS_MANAGER_AUTH_EDIT'), 'mams');
		// Built the actions for new and existing records.
		if ($isNew) 
		{
			// For new records, check the create permission.
			JToolBarHelper::apply('auth.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('auth.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('auth.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::cancel('auth.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::apply('auth.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('auth.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('auth.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::custom('auth.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			JToolBarHelper::cancel('auth.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$isNew = $this->item->auth_id == 0;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_MAMS_AUTH_CREATING') : JText::_('COM_MAMS_AUTH_EDITING'));
		$document->addScript(JURI::root() . $this->script);
		JText::script('COM_MAMS_AUTH_ERROR_UNACCEPTABLE');
	}
}
