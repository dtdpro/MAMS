<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldAuthors extends JFormField
{
	protected $type = 'Authors';

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
		$query = 'SELECT CONCAT(auth_id,":",auth_alias) AS value, auth_name AS text' .
				' FROM #__mams_authors' .
				' ORDER BY auth_name';
		$db->setQuery($query);
		$html[] = '<select name="'.$this->name.'" class="inputbox" '.$attr.'>';
		$html[] = '<option value="">'.JText::_('COM_MAMS_SELECT_AUTHOR').'</option>';
		$html[] = JHtml::_('select.options',$db->loadObjectList(),"value","text",$this->value);
		$html[] = '</select>';		

		return implode($html);
	}
}
