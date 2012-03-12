<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artdload.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artdload
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla table library
jimport('joomla.database.table');

/**
 * MAMS Article Download Table
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artauht
 * @since		1.0
 */
class MAMSTableArtDload extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__mams_artdl', 'ad_id', $db);
	}
	
	
	
}