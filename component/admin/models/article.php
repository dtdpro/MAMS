<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

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
		$table = JTable::getInstance($type, $prefix, $config);
		$table->setColumnAlias('published','state');
		return $table;
	}

	
	public function getForm($data = array(), $loadData = true) 
	{
        $cfg = MAMSHelper::getConfig();
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
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_mams.article.' . (int) $id))	|| ($id == 0 && !$user->authorise('core.edit.state', 'com_mams')))
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
		$item = ArrayHelper::toObject($properties, 'JObject');
	
		if (property_exists($item, 'art_fielddata'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->art_fielddata);
			$item->art_fielddata = $registry->toArray();

			$groups = $this->getAdditionalFields();
			foreach ($groups as &$g) {
				foreach ( $g->fields as $f ) {
					switch ( $f->field_type ) {
						case "auths":
							$item->art_fielddata[$f->field_name] = [];
							foreach ($this->getAuthors($item->art_id,$f->field_id) as $a) {
								$addAuth = new stdClass();
								$addAuth->auth = $a;
								$item->art_fielddata[$f->field_name][] = $addAuth;
							}
							break;
						case "media":
							$item->art_fielddata[$f->field_name] = [];
							foreach ($this->getMedias($item->art_id,$f->field_id) as $a) {
								$addMedia = new stdClass();
								$addMedia->media = $a;
								$item->art_fielddata[$f->field_name][] = $addMedia;
							}
							break;
						case "images":
							$item->art_fielddata[$f->field_name] = [];
							foreach ($this->getImages($item->art_id,$f->field_id) as $a) {
								$addImage = new stdClass();
								$addImage->image = $a;
								$item->art_fielddata[$f->field_name][] = $addImage;
							}
							break;
						case "dloads":
							$item->art_fielddata[$f->field_name] = [];
							foreach ($this->getDloads($item->art_id,$f->field_id) as $a) {
								$addDload = new stdClass();
								$addDload->dload = $a;
								$item->art_fielddata[$f->field_name][] = $addDload;
							}
							break;
						case "links":
							$item->art_fielddata[$f->field_name] = [];
							foreach ($this->getLinks($item->art_id,$f->field_id) as $a) {
								$addLink = new stdClass();
								$addLink->link = $a;
								$item->art_fielddata[$f->field_name][] = $addLink;
							}
							break;
					}
				}
			}
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
		
		//Taxonomy
		if (!empty($item->art_id))
		{
			//Tags
			$item->tags = [];
			foreach ($this->getTags($item->art_id) as $t) {
				$addTag = new stdClass();
				$addTag->tag = $t;
				$item->tags[] = $addTag;
			}
				
			//Cats
			$item->cats = [];
			foreach ($this->getCats($item->art_id) as $c) {
				$addCat = new stdClass();
				$addCat->cat = $c;
				$item->cats[] = $addCat;
			}

			//Authors
			$item->authors = [];
			foreach ($this->getAuthors($item->art_id) as $a) {
				$addAuth = new stdClass();
				$addAuth->auth = $a;
				$item->authors[] = $addAuth;
			}

			//Medias
			$item->medias = [];
			foreach ($this->getMedias($item->art_id,6) as $a) {
				$addMedia = new stdClass();
				$addMedia->media = $a;
				$item->medias[] = $addMedia;
			}

			//Images
			$item->images = [];
			foreach ($this->getImages($item->art_id,10) as $a) {
				$addImage = new stdClass();
				$addImage->image = $a;
				$item->images[] = $addImage;
			}

			//Downloads
			$item->dloads = [];
			foreach ($this->getDloads($item->art_id,7) as $a) {
				$addDload = new stdClass();
				$addDload->dload = $a;
				$item->dloads[] = $addDload;
			}

			//Links
			$item->links = [];
			foreach ($this->getLinks($item->art_id,8) as $a) {
				$addLink = new stdClass();
				$addLink->link = $a;
				$item->links = $addLink;
			}
		}

		if ($item->art_publish_down == "0000-00-00") {
			$item->art_publish_down = JFactory::getDbo()->getNullDate();
		}
	
		return $item;
	}

	public function delete(&$pks)
	{
		$db	= $this->getDbo();
		$pks = (array) $pks;
		$table = $this->getTable();

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				if ($this->canDelete($table))
				{
					$context = $this->option . '.' . $this->name;

					//Cats
					$query	= $db->getQuery(true);
					$query->delete();
					$query->from('#__mams_artcat');
					$query->where('ac_art = '.$table->art_id);
					$db->setQuery((string)$query);
					$db->execute();

					//Tags
					$query	= $db->getQuery(true);
					$query->delete();
					$query->from('#__mams_arttag');
					$query->where('at_art = '.$table->art_id);
					$db->setQuery((string)$query);
					$db->execute();

					//Authors
					$query	= $db->getQuery(true);
					$query->delete();
					$query->from('#__mams_artauth');
					$query->where('aa_art = '.$table->art_id);
					$db->setQuery((string)$query);
					$db->execute();

                    //Medias
                    $query	= $db->getQuery(true);
                    $query->delete();
                    $query->from('#__mams_artmed');
                    $query->where('am_art = '.$table->art_id);
                    $db->setQuery((string)$query);
                    $db->execute();

                    //Downloads
                    $query	= $db->getQuery(true);
                    $query->delete();
                    $query->from('#__mams_artdl');
                    $query->where('ad_art = '.$table->art_id);
                    $db->setQuery((string)$query);
                    $db->execute();

                    //Images
                    $query	= $db->getQuery(true);
                    $query->delete();
                    $query->from('#__mams_artimg');
                    $query->where('ai_art = '.$table->art_id);
                    $db->setQuery((string)$query);
                    $db->execute();

                    //Links
                    $query	= $db->getQuery(true);
                    $query->delete();
                    $query->from('#__mams_artlinks');
                    $query->where('al_art = '.$table->art_id);
                    $db->setQuery((string)$query);
                    $db->execute();

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());

						return false;
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();

					if ($error)
					{
						JLog::add($error, JLog::WARNING, 'jerror');

						return false;
					}
					else
					{
						JLog::add(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), JLog::WARNING, 'jerror');

						return false;
					}
				}
			}
			else
			{
				$this->setError($table->getError());

				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}


	public function save($data)
	{
		$db	= $this->getDbo();
		$table = $this->getTable();
		$cfg = MAMSHelper::getConfig();
		
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;
		
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
		
			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}
		
			// Clean the cache.
			$this->cleanCache();
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

        //Cats
        $query = $db->getQuery(true);
        $query->delete();
        $query->from('#__mams_artcat');
        $query->where('ac_art = ' . $table->art_id);
        $db->setQuery((string)$query);
        $db->execute();
        if ((!empty($data['cats']))) { // && $data['cats'][0]->cat != ''
            $actable = $this->getTable("Artcat", "MAMSTable");
            $order = 0;
            foreach ($data['cats'] as $cat) {
                $actable->ac_id = 0;
                $actable->ac_cat = $cat['cat'];
                $actable->ac_art = $table->art_id;
                $actable->published = 1;
                $actable->ordering = $order;
                $actable->store();
                $order++;
            }
        }

		//Tags
		$query = $db->getQuery(true);
		$query->delete();
		$query->from('#__mams_arttag');
		$query->where('at_art = ' . $table->art_id);
		$db->setQuery((string)$query);
		$db->execute();
		if ((!empty($data['tags']))) {// && $data['tags'][0] != ''
			$actable = $this->getTable("Arttag", "MAMSTable");
			$order = 0;
			foreach ($data['tags'] as $tag) {
				$actable->at_id = 0;
				$actable->at_tag = $tag['tag'];
				$actable->at_art = $table->art_id;
				$actable->published = 1;
				$actable->ordering = $order;
				$actable->store();
				$order++;
			}
		}

        //Authors
        $query = $db->getQuery(true);
        $query->delete();
        $query->from('#__mams_artauth');
        $query->where('aa_art = ' . $table->art_id);
        $query->where('aa_field = 5');
        $db->setQuery((string)$query);
        $db->execute();
        if ((!empty($data['authors']))) {//&& $data['authors'][0] != ''
            $aatable = $this->getTable("Artauth", "MAMSTable");
            $order = 0;
            foreach ($data['authors'] as $auth) {
                $aatable->aa_id = 0;
                $aatable->aa_auth = $auth['auth'];
                $aatable->aa_art = $table->art_id;
                $aatable->published = 1;
                $aatable->ordering = $order;
                $aatable->store();
                $order++;
            }
        }

		//Medias
		$query = $db->getQuery(true);
		$query->delete();
		$query->from('#__mams_artmed');
		$query->where('am_art = ' . $table->art_id);
		$query->where('am_field = 6');
		$db->setQuery((string)$query);
		$db->execute();
		if ((!empty($data['medias']))) {
			$amtable = $this->getTable("Artmed", "MAMSTable");
			$order = 0;
			foreach ($data['medias'] as $media) {
				$amtable->am_id = 0;
				$amtable->am_media = $media['media'];
				$amtable->am_art = $table->art_id;
				$amtable->am_field = 6;
				$amtable->published = 1;
				$amtable->ordering = $order;
				$amtable->store();
				$order++;
			}
		}

		//Images
		$query = $db->getQuery(true);
		$query->delete();
		$query->from('#__mams_artimg');
		$query->where('ai_art = ' . $table->art_id);
		$query->where('ai_field = 10');
		$db->setQuery((string)$query);
		$db->execute();
		if ((!empty($data['images']))) {
			$aitable = $this->getTable("Artimg", "MAMSTable");
			$order = 0;
			foreach ($data['images'] as $image) {
				$aitable->ai_id = 0;
				$aitable->ai_image = $image['image'];
				$aitable->ai_art = $table->art_id;
				$aitable->ai_field = 10;
				$aitable->published = 1;
				$aitable->ordering = $order;
				$aitable->store();
				$order++;
			}
		}

		//Downlaods
		$query = $db->getQuery(true);
		$query->delete();
		$query->from('#__mams_artdl');
		$query->where('ad_art = ' . $table->art_id);
		$query->where('ad_field = 7');
		$db->setQuery((string)$query);
		$db->execute();
		if ((!empty($data['dloads']))) {
			$adtable = $this->getTable("Artdload", "MAMSTable");
			$order = 0;
			foreach ($data['dloads'] as $dload) {
				$adtable->ad_id = 0;
				$adtable->ad_dload = $dload['dload'];
				$adtable->ad_art = $table->art_id;
				$adtable->ad_field = 7;
				$adtable->published = 1;
				$adtable->ordering = $order;
				$adtable->store();
				$order++;
			}
		}

		//Links
		$query = $db->getQuery(true);
		$query->delete();
		$query->from('#__mams_artlinks');
		$query->where('al_art = ' . $table->art_id);
		$query->where('al_field = 8');
		$db->setQuery((string)$query);
		$db->execute();
		if ((!empty($data['links']))) {
			$altable = $this->getTable("Artlink", "MAMSTable");
			$order = 0;
			foreach ($data['links'] as $link) {
				$altable->al_id = 0;
				$altable->al_link = $link['link'];
				$altable->al_art = $table->art_id;
				$altable->al_field = 8;
				$altable->published = 1;
				$altable->ordering = $order;
				$altable->store();
				$order++;
			}
		}

		$groups = $this->getAdditionalFields();
		foreach ($groups as &$g) {
			foreach ( $g->fields as $f ) {
				switch ( $f->field_type ) {
					case "auths":
						$query = $db->getQuery(true);
						$query->delete();
						$query->from('#__mams_artauth');
						$query->where('aa_art = ' . $table->art_id);
						$query->where('aa_field = '.$f->field_id);
						$db->setQuery((string)$query);
						$db->execute();
						if ((!empty($data['art_fielddata'][$f->field_name]))) {
							$aatable = $this->getTable("Artauth", "MAMSTable");
							$order = 0;
							foreach ($data['art_fielddata'][$f->field_name] as $auth) {
								$aatable->aa_id = 0;
								$aatable->aa_auth = $auth['auth'];
								$aatable->aa_art = $table->art_id;
								$aatable->aa_field = $f->field_id;
								$aatable->published = 1;
								$aatable->ordering = $order;
								$aatable->store();
								$order++;
							}
						}
						unset($data['art_fielddata'][$f->field_name]);
						break;
					case "media":
						$query = $db->getQuery(true);
						$query->delete();
						$query->from('#__mams_artmed');
						$query->where('am_art = ' . $table->art_id);
						$query->where('am_field = '.$f->field_id);
						$db->setQuery((string)$query);
						$db->execute();
						if ((!empty($data['art_fielddata'][$f->field_name]))) {
							$aatable = $this->getTable("Artmed", "MAMSTable");
							$order = 0;
							foreach ($data['art_fielddata'][$f->field_name] as $media) {
								$aatable->am_id = 0;
								$aatable->am_media = $media['media'];
								$aatable->am_art = $table->art_id;
								$aatable->am_field = $f->field_id;
								$aatable->published = 1;
								$aatable->ordering = $order;
								$aatable->store();
								$order++;
							}
						}
						unset($data['art_fielddata'][$f->field_name]);
						break;
					case "images":
						$query = $db->getQuery(true);
						$query->delete();
						$query->from('#__mams_artimg');
						$query->where('ai_art = ' . $table->art_id);
						$query->where('ai_field = '.$f->field_id);
						$db->setQuery((string)$query);
						$db->execute();
						if ((!empty($data['art_fielddata'][$f->field_name]))) {
							$aitable = $this->getTable("Artimg", "MAMSTable");
							$order = 0;
							foreach ($data['art_fielddata'][$f->field_name] as $image) {
								$aitable->ai_id = 0;
								$aitable->ai_image = $image['image'];
								$aitable->ai_art = $table->art_id;
								$aitable->ai_field = $f->field_id;
								$aitable->published = 1;
								$aitable->ordering = $order;
								$aitable->store();
								$order++;
							}
						}
						unset($data['art_fielddata'][$f->field_name]);
						break;
					case "dloads":
						$query = $db->getQuery(true);
						$query->delete();
						$query->from('#__mams_artdl');
						$query->where('ad_art = ' . $table->art_id);
						$query->where('ad_field = '.$f->field_id);
						$db->setQuery((string)$query);
						$db->execute();
						if ((!empty($data['art_fielddata'][$f->field_name]))) {
							$adtable = $this->getTable("Artdload", "MAMSTable");
							$order = 0;
							foreach ($data['art_fielddata'][$f->field_name] as $dload) {
								$adtable->ad_id = 0;
								$adtable->ad_dload = $dload['dload'];
								$adtable->ad_art = $table->art_id;
								$adtable->ad_field = $f->field_id;
								$adtable->published = 1;
								$adtable->ordering = $order;
								$adtable->store();
								$order++;
							}
						}
						break;
					case "links":
						$query = $db->getQuery(true);
						$query->delete();
						$query->from('#__mams_artlinks');
						$query->where('al_art = ' . $table->art_id);
						$query->where('al_field = '.$f->field_id);
						$db->setQuery((string)$query);
						$db->execute();
						if ((!empty($data['art_fielddata'][$f->field_name]))) {
							$altable = $this->getTable("Artlink", "MAMSTable");
							$order = 0;
							foreach ($data['art_fielddata'][$f->field_name] as $link) {
								$altable->al_id = 0;
								$altable->al_link = $link['link'];
								$altable->al_art = $table->art_id;
								$altable->al_field = $f->field_id;
								$altable->published = 1;
								$altable->ordering = $order;
								$altable->store();
								$order++;
							}
						}
						break;
				}
			}
		}
		
		return true;
	}
	
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$date	= JFactory::getDate();
		$data = $app->getUserState('com_mams.edit.article.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
			if ($data->art_id == 0) {
				$data->set('art_sec', $app->input->getInt('art_sec', $app->getUserState('com_mams.articles.filter.sec')));
				$data->set('art_publish_up', $date->toSql());
				$data->set('art_publish_down', $this->_db->getNullDate());
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
		$query->where("published >= 0");
		$query->order("ordering asc");
		$db->setQuery($query);
		$groups = $db->loadObjectList();
		foreach ($groups as &$g) {
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from("#__mams_article_fields");
			$query->where("field_group = ".$g->group_id);
			$query->where("published >= 0");
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
					case "textfield":
						$formxml .= '<field name="'.$f->field_name.'" type="text" default="" label="'.$f->field_title.'" description="" />';
						break;
					case "textbox":
						$formxml .= '<field name="'.$f->field_name.'" type="textarea" default="" label="'.$f->field_title.'" description="" filter="safehtml"/>';
						break;
					case "editor":
						$formxml .= '<field name="'.$f->field_name.'" label ="'.$f->field_title.'" type="editor" filter="raw" />';
						break;
					case "auths":
						$formxml .= '<field name="'.$f->field_name.'" type="subform" label="'.$f->field_title.'" layout="joomla.form.field.subform.repeatable-table" icon="list" multiple="true" >';
						$formxml .= '<form hidden="true" name="auth_options" repeat="true"><field name="auth" type="SelectAuthors" description="" label="" required="false" class="inputbox span12 small" /></form>';
						$formxml .= '</field>';
						break;
					case "media":
						$formxml .= '<field name="'.$f->field_name.'" type="subform" label="'.$f->field_title.'" layout="joomla.form.field.subform.repeatable-table" icon="list" multiple="true" >';
						$formxml .= '<form hidden="true" name="media_options" repeat="true"><field name="media" type="SelectMedias" description="" label="" required="false" class="inputbox span12 small" /></form>';
						$formxml .= '</field>';
						break;
					case "images":
						$formxml .= '<field name="'.$f->field_name.'" type="subform" label="'.$f->field_title.'" layout="joomla.form.field.subform.repeatable-table" icon="list" multiple="true" >';
						$formxml .= '<form hidden="true" name="image_options" repeat="true"><field name="image" type="SelectImages" description="" label="" required="false" class="inputbox span12 small" /></form>';
						$formxml .= '</field>';
						break;
					case "dloads":
						$formxml .= '<field name="'.$f->field_name.'" type="subform" label="'.$f->field_title.'" layout="joomla.form.field.subform.repeatable-table" icon="list" multiple="true" >';
						$formxml .= '<form hidden="true" name="dload_options" repeat="true"><field name="dload" type="SelectDloads" description="" label="" required="false" class="inputbox span12 small" /></form>';
						$formxml .= '</field>';
						break;
					case "links":
						$formxml .= '<field name="'.$f->field_name.'" type="subform" label="'.$f->field_title.'" layout="joomla.form.field.subform.repeatable-table" icon="list" multiple="true" >';
						$formxml .= '<form hidden="true" name="link_options" repeat="true"><field name="link" type="SelectLinks" description="" label="" required="false" class="inputbox span12 small" /></form>';
						$formxml .= '</field>';
						break;
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
		if (!$db->execute()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		if ($feat) 	{
			foreach ($pks as $i => $pk) {
				$qf = 'INSERT INTO #__mams_artfeat (af_art) VALUES ('.$pk.')';
				$db->setQuery($qf);
				if (!$db->execute()) {
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
		ArrayHelper::toInteger($pks);
	
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

        if ($commands['batch-rmvcat'] != 0)
        {
            if (!$this->batchRemoveCat($commands['batch-rmvcat'], $pks, $contexts))
            {
                return false;
            }

            $done = true;
        }

		if ($commands['batch-addtag'] != 0)
		{
			if (!$this->batchAddTag($commands['batch-addtag'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if ($commands['batch-rmvtag'] != 0)
		{
			if (!$this->batchRemoveTag($commands['batch-rmvtag'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if ($commands['batch-addauth'] != 0)
		{
			if (!$this->batchAddAuthor($commands['batch-addauth'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if ($commands['batch-rmvauth'] != 0)
		{
			if (!$this->batchRemoveAuthor($commands['batch-rmvauth'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

        if ($commands['batch-startdate'] != '')
        {
            if (!$this->batchStartDate($commands['batch-startdate'], $pks, $contexts))
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

    protected function batchStartDate($value, $pks, $contexts)
    {
        // Set the variables
        $user = JFactory::getUser();
        $table = $this->getTable();

        foreach ($pks as $pk) {
            if ($user->authorise('core.edit', $contexts[$pk])) {
                $table->reset();
                $table->load($pk);
                $table->art_publish_up = $value;

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
		$db = JFactory::getDbo();
	
		foreach ($pks as $pk) {
			$exists_query = $db->getQuery(true);
			$exists_query->select("ac_id");
			$exists_query->from('#__mams_artcat');
			$exists_query->where('ac_art = '.(int)$pk);
			$exists_query->where('ac_cat = '.(int)$value);
			$db->setQuery($exists_query);
			$countofcats = $db->loadColumn();

			if (!$countofcats) {
				if ($user->authorise('core.edit', $contexts[$pk]))	{
					$table            = $this->getTable( "ArtCat", "MAMSTable" );
					$table->ac_art    = $pk;
					$table->ac_cat    = $value;
					$table->published = 1;

					$db->setQuery( 'SELECT MAX(ordering) FROM #__mams_artcat WHERE ac_art = "' . $pk . '"' );
					$max = $db->loadResult();

					$table->ordering = $max + 1;

					if ( ! $table->check() ) {
						$this->setError( $table->getError() );

						return false;
					}

					if ( ! $table->store() ) {
						$this->setError( $table->getError() );

						return false;
					}
				}
			} else {
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}
		}
	
		// Clean the cache
		$this->cleanCache();
	
		return true;
	}

    protected function batchRemoveCat($value, $pks, $contexts)
    {
        // Set the variables
        $user = JFactory::getUser();

        if ($user->authorise('core.edit', $contexts[$pk]))	{
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->delete('#__mams_artcat');
            $query->where('ac_art IN ('.implode(",",$pks).')');
            $query->where('ac_cat = '.(int)$value);
            $db->setQuery($query);
            $db->execute();
        } else {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
            return false;
        }

        // Clean the cache
        $this->cleanCache();

        return true;
    }

	protected function batchAddTag($value, $pks, $contexts)
	{
		// Set the variables
		$user = JFactory::getUser();
		$db = JFactory::getDbo();


		foreach ($pks as $pk) {
			$exists_query = $db->getQuery(true);
			$exists_query->select("at_id");
			$exists_query->from('#__mams_arttag');
			$exists_query->where('at_art = '.(int)$pk);
			$exists_query->where('at_tag = '.(int)$value);
			$db->setQuery($exists_query);
			$countoftags = $db->loadColumn();

			if (!$countoftags) {
				if ( $user->authorise( 'core.edit', $contexts[ $pk ] ) ) {
					$table            = $this->getTable( "ArtTag", "MAMSTable" );
					$table->at_art    = $pk;
					$table->at_tag    = $value;
					$table->published = 1;

					$db->setQuery( 'SELECT MAX(ordering) FROM #__mams_arttag WHERE at_art = "' . $pk . '"' );
					$max = $db->loadResult();

					$table->ordering = $max + 1;

					if ( ! $table->check() ) {
						$this->setError( $table->getError() );

						return false;
					}

					if ( ! $table->store() ) {
						$this->setError( $table->getError() );

						return false;
					}
				} else {
					$this->setError( JText::_( 'JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT' ) );

					return false;
				}
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	protected function batchRemoveTag($value, $pks, $contexts)
	{
		// Set the variables
		$user = JFactory::getUser();

		if ($user->authorise('core.edit', $contexts[$pk]))	{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__mams_arttag');
			$query->where('at_art IN ('.implode(",",$pks).')');
			$query->where('at_tag = '.(int)$value);
			$db->setQuery($query);
			$db->execute();
		} else {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
			return false;
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	protected function batchAddAuthor($value, $pks, $contexts)
	{
		// Set the variables
		$user = JFactory::getUser();
		$db = JFactory::getDbo();


		foreach ($pks as $pk) {
			$exists_query = $db->getQuery(true);
			$exists_query->select("aa_id");
			$exists_query->from('#__mams_artauth');
			$exists_query->where('aa_art = '.(int)$pk);
			$exists_query->where('aa_auth = '.(int)$value);
			$exists_query->where('aa_field = 5');
			$db->setQuery($exists_query);
			$countofauths = $db->loadColumn();

			if (!$countofauths) {
				if ( $user->authorise( 'core.edit', $contexts[ $pk ] ) ) {
					$table            = $this->getTable( "ArtAUth", "MAMSTable" );
					$table->aa_art    = $pk;
					$table->aa_auth   = $value;
					$table->published = 1;

					$db->setQuery( 'SELECT MAX(ordering) FROM #__mams_artauth WHERE aa_art = "' . $pk . '"' );
					$max = $db->loadResult();

					$table->ordering = $max + 1;

					if ( ! $table->check() ) {
						$this->setError( $table->getError() );

						return false;
					}

					if ( ! $table->store() ) {
						$this->setError( $table->getError() );

						return false;
					}
				} else {
					$this->setError( JText::_( 'JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT' ) );

					return false;
				}
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	protected function batchRemoveAuthor($value, $pks, $contexts)
	{
		// Set the variables
		$user = JFactory::getUser();

		if ($user->authorise('core.edit', $contexts[$pk]))	{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__mams_artauth');
			$query->where('aa_art IN ('.implode(",",$pks).')');
			$query->where('aa_auth = '.(int)$value);
			$query->where('aa_field = 5');
			$db->setQuery($query);
			$db->execute();
		} else {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
			return false;
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}

	protected function prepareTable($table)
	{
		if (empty($table->art_id)) {
			if (empty($table->ordering)) {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__mams_articles WHERE art_sec = "'.$table->art_sec.'" && art_publish_up = "'.$table->art_publish_up.'"');
				$max = $db->loadResult();

				$table->ordering = $max+1;
			}
			//$table->reorder('art_sec = "'.$table->art_sec.'" && art_publish_up = "'.$table->art_publish_up.'"');
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
	
	protected function getCats($art_id) {
		if (!$art_id) $art_id = 0;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.ac_cat');
		$query->from('#__mams_artcat as a');
		$query->where('a.ac_art = '.$art_id);
		$query->order('a.ordering');
		$db->setQuery($query);
		return $db->loadColumn();
	}

	protected function getTags($art_id) {
		if (!$art_id) $art_id = 0;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.at_tag');
		$query->from('#__mams_arttag as a');
		$query->where('a.at_art = '.$art_id);
		$query->order('a.ordering');
		$db->setQuery($query);
		return $db->loadColumn();
	}
	
	protected function getAuthors($art_id, $field = 5) {
		if (!$art_id) $art_id = 0;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.aa_auth');
		$query->from('#__mams_artauth as a');
		$query->where('a.aa_art = '.$art_id);
		$query->where('a.aa_field = '.$field);
		$query->order('a.ordering');
		$db->setQuery($query);
		return $db->loadColumn();
	}

	protected function getMedias($art_id, $field = 6) {
		if (!$art_id) $art_id = 0;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.am_media');
		$query->from('#__mams_artmed as a');
		$query->where('a.am_art = '.$art_id);
		$query->where('a.am_field = '.$field);
		$query->order('a.ordering');
		$db->setQuery($query);
		return $db->loadColumn();
	}

	protected function getImages($art_id, $field = 10) {
		if (!$art_id) $art_id = 0;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.ai_image');
		$query->from('#__mams_artimg as a');
		$query->where('a.ai_art = '.$art_id);
		$query->where('a.ai_field = '.$field);
		$query->order('a.ordering');
		$db->setQuery($query);
		return $db->loadColumn();
	}

	protected function getDloads($art_id, $field = 7) {
		if (!$art_id) $art_id = 0;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.ad_dload');
		$query->from('#__mams_artdl as a');
		$query->where('a.ad_art = '.$art_id);
		$query->where('a.ad_field = '.$field);
		$query->order('a.ordering');
		$db->setQuery($query);
		return $db->loadColumn();
	}

	protected function getLinks($art_id, $field = 8) {
		if (!$art_id) $art_id = 0;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.al_link');
		$query->from('#__mams_artlinks as a');
		$query->where('a.al_art = '.$art_id);
		$query->where('a.al_field = '.$field);
		$query->order('a.ordering');
		$db->setQuery($query);
		return $db->loadColumn();
	}

}
