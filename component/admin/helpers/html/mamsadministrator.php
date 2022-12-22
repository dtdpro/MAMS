<?php
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

abstract class JHtmlMAMSAdministrator
{	
	public static function featured($value = 0, $i=0, $canChange = true,$taskController="articles")
	{
		JHtml::_('bootstrap.tooltip');
	
		// Array of image, task, title, action
		$states	= array(
				0	=> array('star-empty',	$taskController.'.featured',	'COM_MAMS_DEFEATURED',	'COM_MAMS_TOGGLE_TO_FEATURE'),
				1	=> array('star',	$taskController.'.unfeatured',	'COM_MAMS_FEATURED',		'COM_MAMS_TOGGLE_TO_DEFEATURE'),
		);
		$state	= ArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];
	
		if ($canChange)
		{
			$html	= '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" class="btn btn-micro hasTooltip' . ($value == 1 ? ' active' : '') . '" title="' . JText::_($state[3]) . '"><i class="icon-'
			. $icon . '"></i></a>';
		}
		else
		{
			$html	= '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="' . JText::_($state[2]) . '"><i class="icon-'
			. $icon . '"></i></a>';
		}
	
		return $html;
	}
	
	public static function drilldowns($i, $canEdit = true)
	{
		JHtml::_('bootstrap.tooltip');
	
		if ($canEdit)
		{
			$html	= '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'articles.drilldowns\')" class="btn btn-micro hasTooltip' . '" title="Drill Downs"><i class="icon-list"></i></a>';
		}
		else
		{
			$html = '';
		}
	
		return $html;
	}
}

