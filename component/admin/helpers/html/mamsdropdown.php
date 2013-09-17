<?php
defined('_JEXEC') or die;

abstract class JHtmlMAMSDropdown extends JHtmlDropdown
{

	public static function editarticle($id)
	{
		static::start();

		$option = JFactory::getApplication()->input->getCmd('option');
		$link = 'index.php?option=' . $option;
		$link .= '&task=article.edit&art_id=' . $id;
		$link = JRoute::_($link);

		static::addCustomItem(JText::_('JACTION_EDIT'), $link);
		return;
	}

	public static function editauthor($id)
	{
		static::start();

		$option = JFactory::getApplication()->input->getCmd('option');
		$link = 'index.php?option=' . $option;
		$link .= '&task=auth.edit&auth_id=' . $id;
		$link = JRoute::_($link);

		static::addCustomItem(JText::_('JACTION_EDIT'), $link);
		return;
	}

	public static function editsection($id)
	{
		static::start();

		$option = JFactory::getApplication()->input->getCmd('option');
		$link = 'index.php?option=' . $option;
		$link .= '&task=sec.edit&sec_id=' . $id;
		$link = JRoute::_($link);

		static::addCustomItem(JText::_('JACTION_EDIT'), $link);

		return;
	}

	public static function editcategory($id)
	{
		static::start();

		$option = JFactory::getApplication()->input->getCmd('option');
		$link = 'index.php?option=' . $option;
		$link .= '&task=cat.edit&cat_id=' . $id;
		$link = JRoute::_($link);

		static::addCustomItem(JText::_('JACTION_EDIT'), $link);
		return;
	}

	public static function editdload($id)
	{
		static::start();

		$option = JFactory::getApplication()->input->getCmd('option');
		$link = 'index.php?option=' . $option;
		$link .= '&task=dload.edit&dl_id=' . $id;
		$link = JRoute::_($link);

		static::addCustomItem(JText::_('JACTION_EDIT'), $link);
		return;
	}

	public static function editlink($id)
	{
		static::start();

		$option = JFactory::getApplication()->input->getCmd('option');
		$link = 'index.php?option=' . $option;
		$link .= '&task=link.edit&link_id=' . $id;
		$link = JRoute::_($link);

		static::addCustomItem(JText::_('JACTION_EDIT'), $link);
		return;
	}

	public static function editmedia($id)
	{
		static::start();

		$option = JFactory::getApplication()->input->getCmd('option');
		$link = 'index.php?option=' . $option;
		$link .= '&task=media.edit&med_id=' . $id;
		$link = JRoute::_($link);

		static::addCustomItem(JText::_('JACTION_EDIT'), $link);
		return;
	}
}