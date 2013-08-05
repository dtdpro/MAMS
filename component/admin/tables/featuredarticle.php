<?php

// no direct access
defined('_JEXEC') or die;

class MAMSTableFeaturedarticle extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__mams_artfeat', 'af_id', $db);
	}
}