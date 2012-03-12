<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artmedia.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artmedia
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla table library
jimport('joomla.database.table');

/**
 * MAMS Article Media Table
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artmedia
 * @since		1.0
 */
class MAMSTableArtMedia extends JTable
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