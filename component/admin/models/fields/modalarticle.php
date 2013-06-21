<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldModalArticle extends JFormField
{
	protected $type = 'ModalArticle';

	protected function getInput()
	{
		// Load the javascript
		JHtml::_('behavior.framework');
		JHtml::_('behavior.modal', 'input.modal');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectMAMSArticle_'.$this->id.'(id, name,sec) {';
		$script[] = '		document.id("'.$this->id.'_id").value = id;';
		$script[] = '		document.id("'.$this->id.'_name").value = name;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Build the script.
		$script = array();
		$script[] = '	window.addEvent("domready", function() {';
		$script[] = '		var div = new Element("div").setStyle("display", "none").injectBefore(document.id("menu-types"));';
		$script[] = '		document.id("menu-types").injectInside(div);';
		$script[] = '	});';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


		// Get the title of the linked chart
		$db = JFactory::getDBO();
		$db->setQuery(
			'SELECT art_title' .
			' FROM #__mams_articles' .
			' WHERE art_id = '.(int) $this->value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}

		if (empty($title)) {
			$title = JText::_('COM_MAMS_SELECT_ARTICLE');
		}

		$link = 'index.php?option=com_mams&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectMAMSArticle_'.$this->id;

		JHtml::_('behavior.modal', 'a.modal');
		$html = "\n".'<div class="fltlft"><input type="text" id="'.$this->id.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('COM_MAMS_CHANGE_ARTICLE_BUTTON').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'.JText::_('COM_MAMS_CHANGE_ARTICLE_BUTTON').'</a></div></div>'."\n";
		// The active newsfeed id field.
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html .= '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return $html;
	}
}
