<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldSelectLinks extends JFormFieldList
{
	protected $type = 'SelectLinks';

	protected function getOptions()
	{

		$db = JFactory::getDBO();
		$query = 'SELECT link_id AS value, link_title AS text' .
		         ' FROM #__mams_links' .
		         ' ORDER BY link_title';
		$db->setQuery($query);

		$options = $db->loadAssocList();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
