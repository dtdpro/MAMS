<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * @version		$Id: medias.php 2012-03-08 $
 * @package		MAMS.Admin
 * @subpackage	medias
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

jimport('joomla.application.component.modellist');

/**
 * MAMS Media Model
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	medias
 * @since		1.0
 */
class MAMSModelMedias extends JModelList
{
	
	public function __construct($config = array())
	{
		
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);

		$accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);
		
		// Load the parameters.
		$params = JComponentHelper::getParams('com_mams');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('m.med_title', 'asc');
	}
	
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('m.*');

		// From the hello table
		$query->from('#__mams_media as m');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = m.access');
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('m.access = '.(int) $access);
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('m.published = '.(int) $published);
		} else if ($published === '') {
			$query->where('(m.published IN (0, 1))');
		}
		
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));
				
		return $query;
	}
}
