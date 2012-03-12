<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artcat.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artcat
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla table library
jimport('joomla.database.table');

/**
 * MAMS Article Category Table
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artcat
 * @since		1.0
 */
class MAMSTableArtCat extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__mams_artcat', 'ac_id', $db);
	}
	
	
	
}