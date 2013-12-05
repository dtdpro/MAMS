<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldImageFields extends JFormField
{
	protected $type = 'ImageFields';

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
		$query =	'SELECT field_id AS value, field_title AS text ' .
					'FROM #__mams_article_fields WHERE field_type = "images" ' .
					'ORDER BY ordering';
		$db->setQuery($query);
		$html[] = '<select name="'.$this->name.'" class="inputbox" '.$attr.'>';
		$html[] = JHtml::_('select.options',$db->loadObjectList(),"value","text",$this->value);
		$html[] = '</select>';

		return implode($html);
	}
}


