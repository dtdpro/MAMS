<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldSelectTags extends JFormFieldList
{
	protected $type = 'SelectTags';

	protected function getOptions()
	{

		$db = JFactory::getDBO();
		$query = 'SELECT tag_id AS value, tag_title AS text' .
		         ' FROM #__mams_tags' .
		         ' ORDER BY tag_title';
		$db->setQuery($query);

		$options = $db->loadAssocList();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
