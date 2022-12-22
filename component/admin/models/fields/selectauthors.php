<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldSelectAuthors extends JFormFieldList
{
	protected $type = 'SelectAuthors';

	protected function getOptions()
	{

		$db = JFactory::getDBO();
		$query = 'SELECT auth_id AS value, CONCAT(auth.auth_fname,IF(auth.auth_mi != "",CONCAT(" ",auth.auth_mi),"")," ",auth.auth_lname,IF(auth.auth_titles != "",CONCAT(", ",auth.auth_titles),""))   AS text' .
		         ' FROM #__mams_authors as auth' .
		         ' WHERE auth_mirror = 0' .
		         ' ORDER BY auth_lname';
		$db->setQuery($query);

		$options = $db->loadAssocList();

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
