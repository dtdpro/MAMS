<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldArticleSecs extends JFormField
{
	protected $type = 'ArticleSecs';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';
		$db = JFactory::getDBO();
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Build the query for the ordering list.
		$query = 'SELECT sec_id AS value, sec_name AS text' .
				' FROM #__mams_secs' .
				' WHERE sec_type = "article"' .
				' ORDER BY sec_name';
		$db->setQuery($query);
		
		$options = $db->loadObjectList();
		
		$user = JFactory::getUser();
		
		if ($this->value == 0)
		{
			foreach ($options as $i => $option)
			{
				if ($user->authorise('core.create.com_mams.sec.' . $option->value) != true)
				{
					unset($options[$i]);
				}
			}
		}
		else
		{
			foreach ($options as $i => $option)
			{
				if ($user->authorise('core.edit.state.com_mams.sec.' . $this->value) != true)
				{
					if ($option->value != $this->value)
					{
						unset($options[$i]);
					}
				}
				if (($user->authorise('core.create.com_mams.sec.' . $option->value) != true) && ($option->value != $this->value))
				{
					{
						unset($options[$i]);
					}
				}
				if (($user->authorise('core.create.com_mams.sec.' . $option->value) != true))
				{
					{
						unset($options[$i]);
					}
				}
			}
		}
		
		$html[] = '<select name="'.$this->name.'" class="inputbox" '.$attr.'>';
		$html[] = JHtml::_('select.options',$options,"value","text",$this->value);
		$html[] = '</select>';		

		return implode($html);
	}
}
