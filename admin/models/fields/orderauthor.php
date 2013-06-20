<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldOrderAuthor extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'OrderAuthor';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get some field values from the form.
		$authId	= (int) $this->form->getValue('auth_id');
		$authSec	= (int) $this->form->getValue('auth_sec');

		// Build the query for the ordering list.
		$query = 'SELECT auth.ordering AS value, CONCAT(auth.auth_fname,IF(auth.auth_mi != "",CONCAT(" ",auth.auth_mi),"")," ",auth.auth_lname,IF(auth.auth_titles != "",CONCAT(", ",auth.auth_titles),""))  AS text' .
				' FROM #__mams_authors as auth' .
				' WHERE auth.auth_sec = '.$authSec.
				' ORDER BY auth.ordering';

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('list.ordering', '', $query, trim($attr), $this->value, $authId ? 0 : 1);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('list.ordering', $this->name, $query, trim($attr), $this->value, $authId ? 0 : 1);
		}

		return implode($html);
	}
}
