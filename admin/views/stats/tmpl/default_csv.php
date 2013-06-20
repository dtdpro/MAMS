<?php
defined('_JEXEC') or die('Restricted access'); 

jimport( 'joomla.filesystem.file' );

$path = JPATH_SITE.'/cache/';
$filename  =  'MAMS_Report' . '-' . date("Y-m-d").'.csv';

$items = $this->items;
$contents = '';	
$contents .= "\"Section\",\"Item\",\"What\",\"When\",\"Who\",\"EMail\",";
if ($this->config->mue) {
	$contents .= "\"Group\",";
}
$contents .= "\"Session\",\"IP Address\"\n";
foreach ($items as $row)
{
	if ($row->mt_user == 0) $row->users_name='Guest User';

	$contents .=  '"'.$row->sec_title.'",';
	$contents .=  '"'.$row->item_title.'",';
	$contents .=  '"';
	switch ($row->mt_type) {
		case "article": $contents .= "Article Page"; break;
		case "author": $contents .= "Author Page"; break;
		case "catlist": $contents .= "Category Article List"; break;
		case "seclist": $contents .= "Section Article LIst"; break;
		case "autlist": $contents .= "Author Article List"; break;
		case "authors": $contents .= "Authors List"; break;
		case "dload": $contents .= "Download"; break;	
	}
	$contents .=  '",';
	$contents .=  '"'.$row->mt_time.'",'; 
	$contents .=  '"'.$row->users_name.'",'; 
	$contents .=  '"'.$row->users_email.'",'; 
	if ($this->config->mue) {
		$contents .= '"'.$row->UserGroup.'",';
	}
	$contents .=  '"'.$row->mt_session.'",'; 
	$contents .=  '"'.$row->mt_ipaddr."\"\n"; 
}

JFile::write($path.$filename,$contents);

 $app = JFactory::getApplication();
 $app->redirect('../cache/'.$filename);