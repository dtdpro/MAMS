<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artauth.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artauth
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla table library
jimport('joomla.database.table');

/**
 * MAMS Article Author Table
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artauht
 * @since		1.0
 */
class MAMSTableArtAuth extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__mams_artauth', 'aa_id', $db);
	}
	
	
	
}