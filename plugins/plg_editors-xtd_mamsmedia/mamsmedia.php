<?php
// no direct access
defined('_JEXEC') or die;

class plgButtonMAMSMedia extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}


	function onDisplay($name)
	{
		$js = "
		function jSelectMedia(id) {
			var tag = '{mvid}' + id + '{/mvid}';
			jInsertEditorText(tag, '".$name."');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHtml::_('behavior.modal');

		/*
		 * Use the built-in element view to select the article.
		 * Currently uses blank class.
		 */
		$link = 'index.php?option=com_mams&amp;view=medias&amp;layout=modal&amp;tmpl=component&amp;'.JSession::getFormToken().'=1';

		$button = new JObject();
		$button->modal=true;
		$button->link=$link;
		$button->text=JText::_('PLG_MAMSMEDIA_BUTTON_ARTICLE');
		$button->name='picture';
		$button->options="{handler: 'iframe', size: {x: 770, y: 400}}";

		return $button;
	}
}
