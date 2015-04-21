<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class MAMSControllerAuth extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_AUTH";

    public function batch($model = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Set the model
        $model = $this->getModel('Auth', '', array());

        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_mams&view=auths' . $this->getRedirectToListAppend(), false));

        return parent::batch($model);
    }
}
