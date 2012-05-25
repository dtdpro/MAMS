<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: dload.php 2012-03-08 $
 * @package		MAMS.Admin
 * @subpackage	dload
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * MAMS Download Edit Controller
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	dload
 * @since		1.0
 */
class MAMSControllerDload extends JControllerForm
{
	protected $text_prefix = "COM_MAMS_DLOAD";
	
	
	/**
	 * The extension for which the categories apply.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $extension;

	/**
	 * Constructor.
	 *
	 * @param  array  $config  An optional associative array of configuration settings.
	 *
	 * @since  1.0
	 * @see    JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	
		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->extension))
		{
			$this->extension = JRequest::getCmd('extension', 'com_mams');
		}
	}
	
	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.0
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'dl_id')
	{
		$append = parent::getRedirectToItemAppend($recordId,$urlVar);
		$append .= '&extension=' . $this->extension;
	
		return $append;
	}
	
	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.0
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&extension=' . $this->extension;
	
		return $append;
	}
}
