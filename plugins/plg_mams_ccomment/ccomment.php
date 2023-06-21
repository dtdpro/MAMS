<?php
/**
 * CComment plugin for MAMS
 * @license http://www.gnu.org/licenses/gpl.html GNU/GPL.
 * @by DtD Productions
 * @Copyright (C) 2023
  */
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgMAMSCComment extends JPlugin
{

	public function onMAMSRenderA($context, &$row, &$params, $page = 0) {
		$input = JFactory::getApplication()->input;

		// don't display comments if we are in print mode and the user doesn't want the comments there
		if ($input->getCmd('print') && !$this->params->get('printView', 0))
		{
			return false;
		}

		JLoader::discover('ccommentHelper', JPATH_SITE . '/components/com_comment/helpers');
		return ccommentHelperUtils::commentInit('com_mams', $row, $params);
	}
	
}
