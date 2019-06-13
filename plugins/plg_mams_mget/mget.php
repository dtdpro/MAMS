<?php
/**
 * MGET plugin for MAMS
 * @license http://www.gnu.org/licenses/gpl.html GNU/GPL.
 * @by DtD Productions
 * @Copyright (C) 2012 
  */
defined( '_JEXEC' ) or die( 'Restricted access' );

class  plgMAMSMGet extends JPlugin
{

	public function onMAMSPrepare(&$text) {
		$user = JFactory::getUser();
		
		$uid=$user->id;
		if ($uid) {
			$username=$user->username;
			$usersname=$user->name;
			$email=$user->email;
		} else {
			$username='Guest';
			$usersname='Guest';
			$email='Guest';
				
		}
		
		//User ID
		$text = str_replace('{mgetuid}',$uid,$text);
		//Username
		$text = str_replace('{mgetuser}',$username,$text);
		//Users Name
		$text = str_replace('{mgetuname}',$usersname,$text);
		//Users Name
		$text = str_replace('{mgetueml}',$email,$text);
		
	}
	
}




?>
