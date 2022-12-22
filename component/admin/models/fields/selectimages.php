<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldSelectImages extends JFormFieldList
{
	protected $type = 'SelectImages';

	protected function getOptions()
	{

		$db = JFactory::getDBO();
		$query = 'SELECT img_id AS value, img_inttitle AS text' .
		         ' FROM #__mams_images' .
		         ' ORDER BY img_inttitle';
		$db->setQuery($query);

		$options = $db->loadAssocList();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
