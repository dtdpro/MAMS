<?php
/**
 * MURI plugin for MAMS
 * @license http://www.gnu.org/licenses/gpl.html GNU/GPL.
 * @by DtD Productions
 * @Copyright (C) 2012 
  */
defined( '_JEXEC' ) or die( 'Restricted access' );

class  plgMAMSMUri extends JPlugin
{

	public function onMAMSPrepare(&$text) {
		$text = str_replace('{muri}',JURI::base( true ),$text);
	}
	
}




?>
