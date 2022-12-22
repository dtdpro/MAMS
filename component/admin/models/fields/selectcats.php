<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldSelectCats extends JFormFieldList
{
	protected $type = 'SelectCats';

	protected function getOptions()
	{

		$db = JFactory::getDBO();
		$query = 'SELECT cat_id AS value, cat_title AS text' .
		         ' FROM #__mams_cats' .
		         ' ORDER BY cat_title';
		$db->setQuery($query);

		$options = $db->loadAssocList();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
