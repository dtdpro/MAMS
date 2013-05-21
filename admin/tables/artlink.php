<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class MAMSTableArtLink extends JTable
{
	function __construct(&$db) 
	{
		parent::__construct('#__mams_artlinks', 'al_id', $db);
	}
	
	
	
}