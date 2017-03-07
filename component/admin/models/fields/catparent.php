<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('list');

class JFormFieldCatParent extends JFormFieldList
{
	protected $type = 'CatParent';

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
		            ->select('a.cat_id AS value, a.cat_title AS text, a.level')
		            ->from('#__mams_cats AS a')
		            ->join('LEFT', $db->quoteName('#__mams_cats') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		if ($this->element['parent'])
		{
			// Prevent parenting to children of this item.
			if ($id = $this->form->getValue('id'))
			{
				$query->join('LEFT', $db->quoteName('#__mams_cats') . ' AS p ON p.cat_id = ' . (int) $id)
				      ->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');
				$rowQuery = $db->getQuery(true);
				$rowQuery->select('a.cat_id AS value, a.cat_title AS text, a.level, a.parent_id')
				         ->from('#__mams_cats AS a')
				         ->where('a.cat_id = ' . (int) $id);
				$db->setQuery($rowQuery);
				$row = $db->loadObject();
			}
		}
		$query->where('a.published IN (0,1)')
		      ->group('a.cat_id, a.cat_title, a.level, a.lft, a.rgt, a.parent_id')
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

		// Add in root Option
		$root_opt = new stdClass();
		$root_opt->value=0;
		$root_opt->text="None";
		$options = array_merge(array($root_opt), $options);

		return $options;
	}

}
