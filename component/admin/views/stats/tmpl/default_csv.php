<?php
defined('_JEXEC') or die('Restricted access');

require JPATH_COMPONENT."/vendor/autoload.php";

jimport( 'joomla.filesystem.file' );

$filename  =  'MAMS_Report' . '-' . date("Y-m-d").'.csv';

$items = $this->items;

$headers = ["Section","Item","Item ID","When","Who","Email"];

if ($this->config->mue) {
	$headers[] = 'Group';
}
$headers[] = 'Session';
$headers[] = 'IP Address';

$dataRows = [];
foreach ($items as $row)
{
	$dataRow = [];
	if ($row->mt_user == 0) $row->users_name='Guest User';


	$dataRow[] =  $row->sec_title;
	$dataRow[] =  $row->item_title;
	$dataRow[] =  $row->mt_item;
	$dataRow[] =  $row->mt_time;
	$dataRow[] =  $row->users_name;
	$dataRow[] =  $row->users_email;
	if ($this->config->mue) {
		$dataRow[] =  $row->UserGroup;
	}
	$dataRow[] =  $row->mt_session;
	$dataRow[] =  $row->mt_ipaddr;
	$dataRows[] = $dataRow;

}

// Set HTTP Headers
$app = JFactory::getApplication();
$app->clearHeaders();
$app->setHeader( "Pragma", "public" );
$app->setHeader( 'Cache-Control', 'no-cache, must-revalidate', true );
$app->setHeader( 'Expires', 'Sat, 26 Jul 1997 05:00:00 GMT', true );
$app->setHeader( 'Content-Type', 'text/csv', true );
$app->setHeader( 'Content-Description', 'File Transfer', true );
$app->setHeader( 'Content-Disposition', 'attachment; filename="' . $filename . '"', true );
$app->setHeader( 'Content-Transfer-Encoding', 'binary', true );
$app->sendHeaders();

// Create CSV Writer
$csv = \League\Csv\Writer::createFromString();

// insert the Headings
$csv->insertOne($headers);

// insert all the records
$csv->insertAll($dataRows);

// CSV content
echo $csv->toString();

// stop
$app->close();

