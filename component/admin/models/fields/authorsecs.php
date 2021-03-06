<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldAuthorSecs extends JFormFieldList
{
	protected $type = 'AuthorSecs';

	protected function getOptions()
	{
		// Initialise variables.
		$options = array();
		$name = (string) $this->element['name'];
		// Let's get the id for the current item, either category or content item.
		$jinput = JFactory::getApplication()->input;
		// For categories the old category is the category id 0 for new category.
		if ($this->element['parent'])
		{
			$oldCat = $jinput->get('id', 0);
		}
		else
			// For items the old category is the category they are in when opened or 0 if new.
		{
			$oldCat = $this->form->getValue($name);
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.sec_id AS value, a.sec_name AS text, a.level')
			->from('#__mams_secs AS a')
			->join('LEFT', $db->quoteName('#__mams_secs') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		if ($this->element['parent'])
		{
			// Prevent parenting to children of this item.
			if ($id = $this->form->getValue('id'))
			{
				$query->join('LEFT', $db->quoteName('#__mams_secs') . ' AS p ON p.sec_id = ' . (int) $id)
					->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');
				$rowQuery = $db->getQuery(true);
				$rowQuery->select('a.secid AS value, a.sec_name AS text, a.level, a.parent_id')
					->from('#__mams_secs AS a')
					->where('a.sec_id = ' . (int) $id);
				$db->setQuery($rowQuery);
				$row = $db->loadObject();
			}
		}
		$query->where('a.published IN (0,1)')->where('a.sec_type = "author"')
			->group('a.sec_id, a.sec_name, a.level, a.lft, a.rgt, a.parent_id')
			->order('a.lft ASC');
		// Get the options.
		$db->setQuery($query);
		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}
		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			// Translate ROOT
			if ($options[$i]->level == 0)
			{
				$options[$i]->text = JText::_('JGLOBAL_ROOT_PARENT');
			}
			$options[$i]->text = str_repeat('- ', $options[$i]->level - 1) . $options[$i]->text;
		}
		// Get the current user object.
		$user = JFactory::getUser();

		if (isset($row) && !isset($options[0]))
		{
			if ($row->parent_id == '0')
			{
				$parent = new stdClass;
				$parent->text = JText::_('JGLOBAL_ROOT_PARENT');
				array_unshift($options, $parent);
			}
		}
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
