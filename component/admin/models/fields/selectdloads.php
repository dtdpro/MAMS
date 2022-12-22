<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldSelectDloads extends JFormFieldList
{
	protected $type = 'SelectDloads';

	protected function getOptions()
	{

		$db = JFactory::getDBO();
		$query = 'SELECT dl_id AS value, dl_fname AS text' .
		         ' FROM #__mams_dloads' .
		         ' ORDER BY dl_fname';
		$db->setQuery($query);

		$options = $db->loadAssocList();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
