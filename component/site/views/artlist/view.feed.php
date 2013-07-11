<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class MAMSViewArtList extends JViewLegacy
{
	protected $artlist = Array();
	protected $secinfo = null;
	protected $autinfo = null;
	protected $catinfo = null;
	protected $params = null;
	protected $pagination = null;
	protected $state = null;
	
	public function display($tpl = null)
	{
		$layout = $this->getLayout();
		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		
		
		$this->state = $this->get('State');
		
		switch($layout) {
			case "category": 
				$this->listCategory();
				break;
			case "author": 
				$this->listAuthor();
				break;
			case "section": 
			default:
				$this->listSection();
				break;
		}
		
		$this->genFeed();
	}
	
	protected function listCategory() {
		$app = JFactory::getApplication();
		$model =& $this->getModel();
		$sec=$this->getSecs();
		if (count($sec)) $this->secinfo=$model->getSecInfo($sec);
		$cat=$this->getCats();
		$this->catinfo=$model->getCatInfo($cat);
		if ($this->catinfo) {
			$artids=$model->getCatArts($cat);
			$this->articles=$model->getArticles($artids,$sec);
			$this->pagination = $this->get('Pagination');
			if (count($this->catinfo) == 1) {
				$this->feedtitle =  $this->catinfo[0]->cat_title;
				$this->feeddesc =  $this->catinfo[0]->cat_desc;
			} else {
				$this->feedtitle =  $this->params->get("page_title",$app->getMenu()->getActive()->title);
			}
		}
	}
	
	protected function listSection() {
		$app = JFactory::getApplication();
		$model =& $this->getModel();
		$sec=$this->getSecs();
		$this->secinfo=$model->getSecInfo($sec);
		if ($this->secinfo) {
			$artids=$model->getSecArts($sec); 
			$this->articles=$model->getArticles($artids,$sec);
			$this->pagination = $this->get('Pagination');
			if (count($this->secinfo) == 1) {
				$this->feedtitle = $this->secinfo[0]->sec_name;
				$this->feeddesc = $this->secinfo[0]->sec_desc;
			} else {
				$this->feedtitle = $this->params->get("page_title",$app->getMenu()->getActive()->title);
			}
		}
	}
	
	protected function listAuthor() {
		$app = JFactory::getApplication();
		$model =& $this->getModel();
		$sec=$this->getSecs();
		if (count($sec)) $this->secinfo=$model->getSecInfo($sec);
		$aut=$this->getAuts();
		$this->autinfo=$model->getAutInfo($aut);
		if ($this->autinfo) {
			$artids=$model->getAuthArts($aut);
			$this->articles=$model->getArticles($artids,$sec);
			$this->pagination = $this->get('Pagination');
			if (count($this->autinfo) == 1) {
				$this->feedtitle =  $this->autinfo[0]->auth_fname.(($this->autinfo[0]->auth_mi) ? " ".$this->autinfo[0]->auth_mi : "")." ".$this->autinfo[0]->auth_lname.(($this->autinfo[0]->auth_titles) ? ", ".$this->autinfo[0]->auth_titles : "");
			} else {
				$this->feedtitle =  $this->params->get("page_title",$app->getMenu()->getActive()->title);
			}
		}
	}
	
	protected function getSecs() {
		$secs = array();
		foreach (JFactory::getApplication()->input->get('secid', array(), 'array') as $s) {
			if ((int)$s) $secs[] = (int)$s;
		}
		return $secs;
	}
	
	protected function getCats() {
		$cats = array();
		foreach (JFactory::getApplication()->input->get('catid', array(), 'array') as $c) {
			if ((int)$c) $cats[] = (int)$c;
		}
		return $cats;
	}
	
	protected function getAuts() {
		$auts = array();
		foreach (JFactory::getApplication()->input->get('autid', array(), 'array') as $a) {
			if ((int)$a) $auts[] = (int)$a;
		}
		return $auts;
	}
	
	protected function genFeed() {
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$params = $app->getParams();
		
		// Get some data from the model
		$app->input->set('limit', $app->getCfg('feed_limit'));
		
		$doc->link = JURI::base( true );
		$doc->setGenerator("");
		$doc->setTitle($this->feedtitle);
		$doc->setDescription($this->feeddesc);
		
		foreach ($this->articles as $row)
		{
			// Strip html from feed item title
			$title = $this->escape($row->art_title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
		
			// Url link to article
			$link = JRoute::_("index.php?option=com_mams&view=article&secid=".$a->sec_id.":".$a->sec_alias."&artid=".$a->art_id.":".$a->art_alias);
		
			// Get description, author and date
			$description = $row->art_desc;
			$author = $row->auts[0]->auth_fname.(($row->auts[0]->auth_mi) ? " ".$row->auts[0]->auth_mi : "")." ".$row->auts[0]->auth_lname.(($row->auts[0]->auth_titles) ? ", ".$row->auts[0]->auth_titles : "");
			@$date = ($row->art_publish_up ? date('r', strtotime($row->art_publish_up)) : '');
		
			// Load individual item creator class
			$item = new JFeedItem;
			$item->title = $title;
			$item->link = $link;
			$item->date = $date;
			$item->category = $row->sec_name;
			$item->author = $author;
				
			// Load item description and add div
			$item->description	= '<div class="feed-description">'.$description.'</div>';
		
			// Loads item info into rss array
			$doc->addItem($item);
		}
	}
	
	
}
?>
