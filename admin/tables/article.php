<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: article.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	auth
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla table library
jimport('joomla.database.table');

/**
 * MAMS Article Table
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	article
 * @since		1.0
 */
class MAMSTableArticle extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__mams_articles', 'art_id', $db);
	}
	
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->art_id) {
			// Existing item
			$this->art_modified		= $date->toMySQL();
		} else {
			// New section. A section created on field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->art_added)) {
				$this->art_added = $date->toMySQL();
				$this->art_modified		= $date->toMySQL();
			}
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('Article', 'MAMSTable');
		if ($table->load(array('art_alias'=>$this->art_alias)) && ($table->art_id != $this->art_id || $this->art_id==0)) {
			$this->setError(JText::_('COM_MAMS_ERROR_UNIQUE_ALIAS'));
			return false;
		}
		// Attempt to store the user data.
		return parent::store($updateNulls);
	}
	
	public function check()
	{
		// check for valid name
		if (trim($this->art_title) == '') {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_TITLE'));
			return false;
		}

		// check for existing name
		$query = 'SELECT art_id FROM #__mams_articles WHERE art_title = '.$this->_db->Quote($this->art_title);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->art_id)) {
			$this->setError(JText::_('COM_MAMS_ERR_TABLES_NAME'));
			return false;
		}

		if (empty($this->art_alias)) {
			$this->art_alias = $this->art_title;
		}
		$this->art_alias = JApplication::stringURLSafe($this->art_alias);
		if (trim(str_replace('-','',$this->art_alias)) == '') {
			$this->art_alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		return true;
	}
	
}