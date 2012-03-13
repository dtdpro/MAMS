<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: auth.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	article
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * MAMS Article Edit Model
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	article
 * @since		1.0
 */
class MAMSModelArticle extends JModelAdmin
{
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowEdit($data = array(), $key = 'art_id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_mams.article.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Article', $prefix = 'MAMSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_mams.article', 'article', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		return $form;
	}
	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return 'administrator/components/com_mams/models/forms/article.js';
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_mams.edit.article.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('article.art_id') == 0) {
				
			}
		}
		return $data;
	}
	
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$table = $this->getTable();
		
		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);
			
			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}
		
		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = JArrayHelper::toObject($properties, 'JObject');
		
		if (property_exists($item, 'params'))
		{
			$registry = new JRegistry();
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}
		
		$q = 'SELECT am_media FROM #__mams_artmed WHERE ordering=1 && am_art = '.$item->art_id;
		$this->_db->setQuery($q);
		$item->art_media=$this->_db->loadResult();
		
		return $item;
	}
	
	public function save($data)
	{
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;
		
		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');
		
		// Allow an exception to be thrown.
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}
			
			// Bind the data.
			if (!$table->bind($data))
			{
				$this->setError($table->getError());
				return false;
			}
			
			// Prepare the row for saving
			$this->prepareTable($table);
			
			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}
			
			// Trigger the onContentBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew));
			if (in_array(false, $result, true))
			{
				$this->setError($table->getError());
				return false;
			}
			
			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}
			
			// Clean the cache.
			$this->cleanCache();
			
			// Trigger the onContentAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			
			return false;
		}
		
		$pkName = $table->getKeyName();
		
		if (isset($table->$pkName))
		{
			$this->setState($this->getName() . '.id', $table->$pkName);
		}
		$this->setState($this->getName() . '.new', $isNew);
		
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->delete();
		$query->from('#__mams_artmed');
		$query->where('am_art = '.(int)$table->art_id);
		$db->setQuery((string)$query);
		$db->query();
		
		if (!empty($data['art_media'])) {
				$qc = 'INSERT INTO #__mams_artmed (am_art,am_media,ordering,published) VALUES ('.(int)$table->art_id.','.(int)$data['art_media'].',1,1)';
				$db->setQuery($qc);
				if (!$db->query()) {
					$this->setError($db->getErrorMsg());
					return false;
				} 
		}
		
		return true;
	}
	
}
