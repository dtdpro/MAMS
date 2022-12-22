<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldSelectMedias extends JFormFieldList
{
	protected $type = 'SelectMedias';

	protected function getOptions()
	{

		$db = JFactory::getDBO();
		$query = 'SELECT med_id AS value, med_inttitle AS text' .
		         ' FROM #__mams_media' .
		         ' ORDER BY med_inttitle';
		$db->setQuery($query);

		$options = $db->loadAssocList();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
