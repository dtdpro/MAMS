<?php
// No direct access to this file
defined('_JEXEC') or die;

abstract class MAMSHelper
{
	public static function addSubmenu($submenu,$extension) 
	{
		if ($extension=='com_mams') {
			JHtmlSidebar::addEntry(JText::_('COM_MAMS_SUBMENU_MAMS'), 'index.php?option=com_mams', $submenu == 'mams');
			JHtmlSidebar::addEntry(JText::_('COM_MAMS_SUBMENU_ARTICLES'),'index.php?option=com_mams&view=articles',$submenu == 'articles');
			JHtmlSidebar::addEntry(JText::_('COM_MAMS_SUBMENU_SECS'),'index.php?option=com_mams&view=secs',$submenu == 'secs');
			JHtmlSidebar::addEntry(JText::_('COM_MAMS_SUBMENU_CATS'),'index.php?option=com_mams&view=cats',$submenu == 'cats');
			JHtmlSidebar::addEntry(JText::_('COM_MAMS_SUBMENU_AUTHS'),'index.php?option=com_mams&view=auths',$submenu == 'auths');
			JHtmlSidebar::addEntry(JText::_('COM_MAMS_SUBMENU_DLOADS'),'index.php?option=com_mams&view=dloads&extension=com_mams',$submenu == 'dloads');
			JHtmlSidebar::addEntry(JText::_('COM_MAMS_SUBMENU_MEDIAS'),'index.php?option=com_mams&view=medias&extension=com_mams',$submenu == 'medias');
			JHtmlSidebar::addEntry(JText::_('COM_MAMS_SUBMENU_FEATMEDIAS'),'index.php?option=com_mams&view=featuredmedia',$submenu == 'featuredmedia');
			JHtmlSidebar::addEntry(JText::_('COM_MAMS_SUBMENU_FEATARTS'),'index.php?option=com_mams&view=featuredarticle',$submenu == 'featuredarticle');
			JHtmlSidebar::addEntry(JText::_('COM_MAMS_SUBMENU_STATS'),'index.php?option=com_mams&view=stats',$submenu == 'stats');
		} else {
			// Try to find the component helper.
			$eName	= str_replace('com_', '', $extension);
			$file	= JPath::clean(JPATH_ADMINISTRATOR.'/components/'.$extension.'/helpers/'.$eName.'.php');
			
			if (file_exists($file)) {
				require_once $file;
			
				$prefix	= ucfirst(str_replace('com_', '', $extension));
				$cName	= $prefix.'Helper';
			
				if (class_exists($cName)) {
			
					if (is_callable(array($cName, 'addSubmenu'))) {
						$lang = JFactory::getLanguage();
						// loading language file from the administrator/language directory then
						// loading language file from the administrator/components/*extension*/language directory
						$lang->load($extension, JPATH_BASE, null, false, false)
						||	$lang->load($extension, JPath::clean(JPATH_ADMINISTRATOR.'/components/'.$extension), null, false, false)
						||	$lang->load($extension, JPATH_BASE, $lang->getDefault(), false, false)
						||	$lang->load($extension, JPath::clean(JPATH_ADMINISTRATOR.'/components/'.$extension), $lang->getDefault(), false, false);
						call_user_func(array($cName, 'addSubmenu'), $submenu);
					}
				}
			}
		}
	}
	
	static function getSections($type = "article")
	{
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT sec_id AS value, sec_name AS text' .
			' FROM #__mams_secs WHERE sec_type = "'.$type.'" ' .
			' ORDER BY sec_name'
		);
		$options = $db->loadObjectList();
		
		// Check for a database error.
		if ($db->getErrorNum())
		{
		JError::raiseNotice(500, $db->getErrorMsg());
		return null;
		}
		
		return $options;
	}

	/**
	 * Get configuration for component.
	 *
	 * @return object The current config parameters
	 *
	 * @since 1.00
	 */
	function getConfig() {
		$menuConfig = JComponentHelper::getParams('com_mams');
		$mamscfg = $menuConfig->toObject();
		return $mamscfg;
	}
	
}
