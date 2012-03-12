<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/**
 * @version		$Id: artdload.php 2012-03-12 $
 * @package		MAMS.Admin
 * @subpackage	artdload
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * MAMS Article Download Edit Model
 *
 * @static
 * @package		MAMS.Admin
 * @subpackage	artdload
 * @since		1.0
 */
class MAMSModelArtDload extends JModelAdmin
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
	protected function allowEdit($data = array(), $key = 'ad_id')
	{
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_mams.artdload.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
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
	public function getTable($type = 'ArtDload', $prefix = 'MAMSTable', $config = array()) 
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
		$form = $this->loadForm('com_mams.artdload', 'artdload', array('control' => 'jform', 'load_data' => $loadData));
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
		return 'administrator/components/com_mams/models/forms/artdload.js';
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
		$data = JFactory::getApplication()->getUserState('com_mams.edit.artdload.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($this->getState('artdload.ad_id') == 0) {
				$app = JFactory::getApplication();
				$data->set('ad_art', JRequest::getInt('ad_art', $app->getUserState('com_mams.artdloads.filter.article')));
			}
		}
		return $data;
	}
	
	/**
	* Prepare and sanitise the table prior to saving.
	*
	* @since 1.6
	*/
	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		
		if (empty($table->ad_id)) {
			// Set the values
			
			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__mams_artdload WHERE ad_art = "'.$table->ad_art.'"');
				$max = $db->loadResult();
				
				$table->ordering = $max+1;
			}
		}
		else {
			// Set the values
		}
	}
	
	/**
	* A protected method to get a set of ordering conditions.
	*
	* @param object A record object.
	* @return array An array of conditions to add to add to ordering queries.
	* @since 1.6
	*/
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'ad_art = '.(int) $table->ad_art;
		return $condition;
	}
	
}
