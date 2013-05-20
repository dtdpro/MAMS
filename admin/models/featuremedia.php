<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

require_once dirname(__FILE__).'/media.php';

class MAMSModelFeatureMedia extends MAMSModelMedia
{
	public function getTable($type = 'FeaturedMedia', $prefix = 'MAMSTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	protected function getReorderConditions($table)
	{
		$condition = array();
		return $condition;
	}
}