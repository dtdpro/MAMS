<?php

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldArticleSecs extends JFormFieldList
{
	public $type = 'ArticleSecs';

	protected function getOptions()
	{
		// Initialize variables.
		$options = array();
		$db = JFactory::getDBO();
		
		// Build the query for the ordering list.
		$query = $db->getQuery(true);
		$query->select('sec_id AS value, sec_name AS text');
		$query->from('#__mams_secs');
		$query->where('sec_type = "article" && published > 0');
		$query->order('sec_name');
		$db->setQuery($query);
		
		$options = $db->loadObjectList();
		
		$user = JFactory::getUser();
		
		if ($this->value == 0)
		{
			foreach ($options as $i => $option)
			{
				if ($user->authorise('core.create','com_mams.sec.' . $option->value) != true)
				{
					unset($options[$i]); 
				}
			}
		}
		else
		{
			foreach ($options as $i => $option)
			{ 
				if ($user->authorise('core.edit.state','com_mams.sec.' . $this->value) != true)
				{
					if ($option->value != $this->value)
					{
						unset($options[$i]);
					}
				}
				if (($user->authorise('core.create','com_mams.sec.' . $option->value) != true) && ($option->value != $this->value))
				{
					{
						unset($options[$i]);
					}
				}
				if (($user->authorise('core.create','com_mams.sec.' . $option->value) != true))
				{
					{
						unset($options[$i]);
					}
				}
			}
		}
			

		return $options;
	}
}
