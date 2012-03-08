<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: dload.php 2012-03-08 $
 * @package		MAMS.Admin
 * @subpackage	dload
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla table library
jimport('joomla.database.table');

/**
 * MAMS Download Table
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	dload
 * @since		1.0
 */
class MAMSTableDload extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__mams_dloads', 'dl_id', $db);
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if (!intval($this->dl_added)) {
			$this->dl_added = $date->toMySQL();
		}
		
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
	
	public function check()
	{
		// check for valid name
		if (trim($this->dl_fname) == '' || trim($this->dl_lname) == '') {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_TITLE'));
			return false;
		}

		// check for existing name
		$query = 'SELECT dl_id FROM #__mams_cats WHERE dl_fname = '.$this->_db->Quote($this->dl_fname);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->dl_id)) {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_NAME'));
			return false;
		}

		return true;
	}
	
}