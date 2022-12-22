<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldSelectSecs extends JFormFieldList
{
	protected $type = 'SelectSecs';

	protected function getOptions()
	{

		$db = JFactory::getDBO();
		$query = 'SELECT sec_id AS value, sec_name AS text' .
		         ' FROM #__mams_secs' .
		         ' WHERE sec_type = "article"' .
		         ' ORDER BY sec_name';
		$db->setQuery($query);

		$options = $db->loadAssocList();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
