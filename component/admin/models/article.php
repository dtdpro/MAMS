<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class MAMSModelArticle extends JModelAdmin
{
	
	public $typeAlias = 'com_mams.article';
	
	protected function canDelete($record)
	{
		if (!empty($record->art_id))
		{
			if ($record->state != -2)
			{
				return;
			}
			$user = JFactory::getUser();
	
			return parent::canDelete($record);
		}
	}
	protected function canEditState($record)
	{
		$user = JFactory::getUser();
	
		// Check for existing article.
		if (!empty($record->art_id))
		{
			return $user->authorise('core.edit.state', 'com_mams.article.' . (int) $record->art_id);
		}
		// New article, so check against the category.
		elseif (!empty($record->art_sec))
		{
			return $user->authorise('core.edit.state', 'com_mams.sec.' . (int) $record->art_sec);
		}
		// Default to component settings if neither article nor category known.
		else
		{
			return parent::canEditState('com_mams');
		}
	}
	
	public function getTable($type = 'Article', $prefix = 'MAMSTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_mams.article', 'article', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) 
		{
			return false;
		}
		$jinput = JFactory::getApplication()->input;
		
		$id = $jinput->get('art_id', 0);
		
		// Determine correct permissions to check.
		if ($id)
		{
			// Existing record. Can only edit in selected sections.
			$form->setFieldAttribute('art_sec', 'action', 'core.edit');
			// Existing record. Can only edit own articles in selected sections.
			$form->setFieldAttribute('art_sec', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected sections.
			$form->setFieldAttribute('art_sec', 'action', 'core.create');
		}
		
		$user = JFactory::getUser();
		
		// Check for existing article.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_mams.article.' . (int) $id))	|| ($id == 0 && !$user->authorise('core.edit.state', 'com_mams'))
		)
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');
		
			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}
		
		return $form;
	}
	
	public function getItem($pk = null)
	{
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
	
		if (property_exists($item, 'art_fielddata'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->art_fielddata);
			$item->art_fielddata = $registry->toArray();
		}
		
		if (property_exists($item, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}
		
		// Convert the metadata field to an array.
		$registry = new JRegistry;
		$registry->loadString($item->metadata);
		$item->metadata = $registry->toArray();
		
		//Tags
		if (!empty($item->art_id))
		{
			$item->tags = new JHelperTags;
			$item->tags->getTagIds($item->art_id, 'com_mams.article');
		}
	
		return $item;
	}
	
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_mams.edit.article.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($data->art_id == 0) {
				$data->set('art_sec', $app->input->getInt('art_sec', $app->getUserState('com_mams.articles.filter.sec')));
			}
		}
		return $data;
	}
	
	protected function getAdditionalFields() {
		$db	= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from("#__mams_article_fieldgroups");
		$query->where("group_id > 1");
		$query->order("ordering asc");
		$db->setQuery($query);
		$groups = $db->loadObjectList();
		foreach ($groups as &$g) {
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from("#__mams_article_fields");
			$query->where("field_group = ".$g->group_id);
			$query->order("ordering asc");
			$db->setQuery($query);
			$g->fields = $db->loadObjectList();
		}
		return $groups;
	}
	
	public function getAdditionalForms($item) {
		$groups = $this->getAdditionalFields();
		foreach ($groups as &$g) {
			$formxml='<?xml version="1.0" encoding="utf-8"?><form><fieldset name="'.$g->group_name.'">';
			$formxml.='<field name="show_'.$g->group_name.'" type="radio" label="Show '.$g->group_title.'" class="btn-group" default="0"><option value="1">Yes</option><option value="0">No</option></field>';
			foreach ($g->fields as $f) {
				switch ($f->field_type) {
					case "textfield": $formxml .=  '<field name="'.$f->field_name.'" type="text" default="" label="'.$f->field_title.'" description="" />'; break;
					case "textbox": $formxml .=  '<field name="'.$f->field_name.'" type="textarea" default="" label="'.$f->field_title.'" description="" filter="safehtml"/>'; break;
					case "editor": $formxml .=  '<field name="'.$f->field_name.'" label ="'.$f->field_title.'" type="editor" filter="raw" />'; break;
				}
			}
			$formxml.='</fieldset></form>';
			$g->form = JForm::getInstance($g->group_name,$formxml, array('control' => 'jform[art_fielddata]'));
			$g->form->bind($item->art_fielddata);
		}
		return $groups;
	}
	
	public function featured(&$pks,$feat)
	{
		// Initialise variables.
		$user = JFactory::getUser();
		$pks = (array) $pks;
		$db	= $this->getDbo();

		$table = $this->getTable('FeaturedArticle', 'MAMSTable');
		
		$query	= $db->getQuery(true);
		$query->delete();
		$query->from('#__mams_artfeat');
		$query->where('af_art IN ('.implode(",",$pks).")");
		$db->setQuery((string)$query);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		if ($feat) 	{
			foreach ($pks as $i => $pk) {
				$qf = 'INSERT INTO #__mams_artfeat (af_art) VALUES ('.$pk.')';
				$db->setQuery($qf);
				if (!$db->query()) {
					$this->setError($db->getErrorMsg());
					return false;
				}
			}
		} 
			
		$table->reorder();
	
		// Clear the component's cache
		$this->cleanCache();
	
		return true;
	}

	public function batch($commands, $pks, $contexts)
	{
		// Sanitize user ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);
	
		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}
	
		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}
	
		$done = false;
	
		if (!empty($commands['assetgroup_id']))
		{
			if (!$this->batchAccess($commands['assetgroup_id'], $pks, $contexts))
			{
				return false;
			}
	
			$done = true;
		}
	
		if (!empty($commands['featassetgroup_id']))
		{
			if (!$this->batchFeatAccess($commands['featassetgroup_id'], $pks, $contexts))
			{
				return false;
			}
	
			$done = true;
		}
	
		if ($commands['featsection_id'] != 0)
		{
			if (!$this->batchSection($commands['featsection_id'], $pks, $contexts))
			{
				return false;
			}
	
			$done = true;
		}
	
		if ($commands['batch-addcat'] != 0)
		{
			if (!$this->batchAddCat($commands['batch-addcat'], $pks, $contexts))
			{
				return false;
			}
	
			$done = true;
		}
	
		if (!$done)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}
	
		// Clear the cache
		$this->cleanCache();
	
		return true;
	}
	
	protected function batchFeatAccess($value, $pks, $contexts)
	{
		// Set the variables
		$user = JFactory::getUser();
		$table = $this->getTable();
	
		foreach ($pks as $pk) {
			if ($user->authorise('core.edit', $contexts[$pk])) {
				$table->reset();
				$table->load($pk);
				$table->feataccess = (int) $value;
	
				if (!$table->check()) { $this->setError($table->getError()); return false; }
	
				if (!$table->store()) { $this->setError($table->getError()); return false; }
			} else {
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}
		}
	
		// Clean the cache
		$this->cleanCache();
	
		return true;
	}

	protected function batchSection($value, $pks, $contexts)
	{
		// Set the variables
		$user = JFactory::getUser();
		$table = $this->getTable();
	
		foreach ($pks as $pk) {
			if ($user->authorise('core.edit', $contexts[$pk]))	{
				$table->reset();
				$table->load($pk);
				$table->art_sec = (int) $value;
	
				if (!$table->check()) {	$this->setError($table->getError()); return false; }
	
				if (!$table->store()) { $this->setError($table->getError()); return false; }
			} else {
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}
		}
	
		// Clean the cache
		$this->cleanCache();
	
		return true;
	}

	protected function batchAddCat($value, $pks, $contexts)
	{
		// Set the variables
		$user = JFactory::getUser();
		
	
		foreach ($pks as $pk) {
			if ($user->authorise('core.edit', $contexts[$pk]))	{
				$table = $this->getTable("ArtCat","MAMSTable");
				$table->ac_art=$pk;
				$table->ac_cat=$value;
				$table->published=1;
				
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__mams_artcat WHERE ac_art = "'.$pk.'"');
				$max = $db->loadResult();
				
				$table->ordering = $max+1;
				
				if (!$table->check()) {	$this->setError($table->getError()); return false; }
	
				if (!$table->store()) { $this->setError($table->getError()); return false; }
			} else {
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}
		}
	
		// Clean the cache
		$this->cleanCache();
	
		return true;
	}

	protected function prepareTable(&$table)
	{
		if (empty($table->art_id)) {
			$table->reorder('art_sec = "'.$table->art_sec.'" && art_publish_up = "'.$table->art_publish_up.'"');
		}
		
		//Increment Version Number
		$table->version++;
	}

	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'art_sec = '.$table->art_sec.' && art_publish_up = '.$table->art_publish_up;
		return $condition;
	}
	
}
