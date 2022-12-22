<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

require_once dirname(__FILE__).'/media.php';

class MAMSModelFeaturemedia extends MAMSModelMedia
{
	public function getTable($type = 'Featuredmedia', $prefix = 'MAMSTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	protected function getReorderConditions($table)
	{
		$condition = array();
		return $condition;
	}
	
	public function defeatured(&$pks)
	{
		// Initialise variables.
		$user = JFactory::getUser();
		$pks = (array) $pks;
		$db	= $this->getDbo();
		$table = $this->getTable('FeaturedMedia', 'MAMSTable');
	
		$query	= $db->getQuery(true);
		$query->delete();
		$query->from('#__mams_mediafeat');
		$query->where('mf_id IN ('.implode(",",$pks).")");
		$db->setQuery((string)$query);
		if (!$db->execute()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
					
		$table->reorder();
	
		// Clear the component's cache
		$this->cleanCache();
	
		return true;
	}
}