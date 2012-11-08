<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artmed.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artmed
 * @copyright	Copyright (C) 2012 DtD Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla table library
jimport('joomla.database.table');

/**
 * MAMS Article Media Table
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artmed
 * @since		1.0
 */
class MAMSTableArtMed extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__mams_artmed', 'am_id', $db);
	}
	
	
	
}