<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldAuthorMirror extends JFormField
{
	protected $type = 'AuthorMirror';

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
		$attr .= $this->element['multiple'] ? ' multiple ' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Build the query for the ordering list.
		$query = 'SELECT auth_id AS value, CONCAT(auth.auth_fname,IF(auth.auth_mi != "",CONCAT(" ",auth.auth_mi),"")," ",auth.auth_lname,IF(auth.auth_titles != "",CONCAT(", ",auth.auth_titles),""))   AS text' .
				' FROM #__mams_authors as auth' .
				' WHERE auth_mirror = 0' .
				' ORDER BY auth_lname';
		$db->setQuery($query);
		$html[] = '<select name="'.$this->name.'" '.$attr.'>';
		$html[] = '<option value="0">None</option>';
		$html[] = JHtml::_('select.options',$db->loadObjectList(),"value","text",$this->value);
		$html[] = '</select>';		

		return implode($html);
	}
}
