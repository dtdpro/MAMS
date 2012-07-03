<?php
defined('_JEXEC') or die;

abstract class JHtmlMAMSAdministrator
{
	/**
	 * @param	int $value	The state value
	 * @param	int $i
	 */
	static function featured($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states	= array(
			0	=> array('disabled.png',	'articles.featured',	'COM_MAMS_DEFEATURED',	'COM_MAMS_TOGGLE_TO_FEATURE'),
			1	=> array('featured.png',		'articles.defeatured',	'COM_MAMS_FEATURED',		'COM_MAMS_TOGGLE_TO_DEFEATURE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$html	= JHtml::_('image', 'admin/'.$state[0], JText::_($state[2]), null, true);
		if ($canChange) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					. $html.'</a>';
		}

		return $html;
	}
}