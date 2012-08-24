<?php


// no direct access
defined('_JEXEC') or die;

class plgButtonMAMSArticle extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}


	function onDisplay($name)
	{
		$js = "
		function jSelectMAMSArticle(id, title, sec) {
			var tag = '<a href=\"index.php?option=com_mams&view=article&secid=' + sec + '&artid='+id+'\">' + title + '</a>';
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
		$link = 'index.php?option=com_mams&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;'.JSession::getFormToken().'=1';

		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_MAMSARTICLE_BUTTON_ARTICLE'));
		$button->set('name', 'article');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");

		return $button;
	}
}
