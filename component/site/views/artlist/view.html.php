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
	protected $error = false;
	
	public function display($tpl = null)
	{
		$layout = $this->getLayout();
		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		
		
		$this->state = $this->get('State');
		
		switch($layout) {
			case "catlist": 
				$this->listCats();
				break;
			case "seclist": 
				$this->listSecs();
				break;
			case "category": 
				$this->listCategory();
				break;
			case "catsec": 
				$this->listCatSec();
				break;
			case "author": 
				$this->listAuthor();
				break;
			case "allsecs": 
				$this->listAll();
				break;
			case "secbycat": 
				$this->listSecbyCat();
				break;
			case "section": 
			default:
				$this->listSection();
				break;
		}
		
		if ($this->error) return false;
		
		//RSS Feed Link
		$link = '&format=feed&limitstart=';
		$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
		$this->document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
		$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
		$this->document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		
		parent::display($tpl);
		if ($layout != "secbycat" && $layout != "catlist" && $layout != "seclist") {
			if ($this->params->get("listview","blog") == "blog") $this->setLayout('artlist');
			else $this->setLayout('artgal');
			parent::display($tpl);
		}

		if ($this->params->get('divwrapper',1)) { echo '</div>'; }
	}
	
	protected function listSecs() {
		$model =& $this->getModel();
		$this->seclist = $model->getSecs($this->params->get("show_count",0));
	}
	
	protected function listCats() {
		$model =& $this->getModel();
		$this->catlist = $model->getCats($this->params->get("show_count",0));
	}
	
	protected function listSecByCat() {
		$model =& $this->getModel();
		$sec=$this->getSecs();
		if (!$sec) {
			JError::raiseError(404, JText::_('COM_MAMS_ARTICLE_NOT_FOUND'));
			$this->error=true;
			return false;
		}
		$this->secinfo=$model->getSecInfo($sec);
		$this->cats = $model->getSecCats($sec);
		if ($this->secinfo && $this->cats) {
			if (count($this->secinfo) == 1) $this->document->setTitle($this->secinfo[0]->sec_name);
			foreach ($this->cats as &$c) {
				$cat = array();
				$cat[] = $c->cat_id;
				$artids=$model->getCatArts($cat);
				$c->artids = $artids;
				$c->articles=$model->getArticles($artids,$sec,false);
			}
		}
	}
	
	protected function listCatSec() {
		$model =& $this->getModel();
		$sec=$this->getSecs();
		if (count($sec)) $this->secinfo=$model->getSecInfo($sec);
		$cat=$this->getCats();
		if (!$cat) {
			JError::raiseError(404, JText::_('COM_MAMS_ARTICLE_NOT_FOUND'));
			$this->error=true;
			return false;
		}
		$this->catinfo=$model->getCatInfo($cat);
		if ($this->catinfo) {
			if (count($this->catinfo) == 1) $this->document->setTitle($this->catinfo[0]->cat_title);
			$artids=$model->getCatArts($cat);
			$this->articles=$model->getArticles($artids,$sec);
			$this->pagination = $this->get('Pagination');
		}
	}
	
	protected function listCategory() {
		$model =& $this->getModel();
		$cat=$this->getCats();
		if (!$cat) {
			JError::raiseError(404, JText::_('COM_MAMS_ARTICLE_NOT_FOUND'));
			$this->error=true;
			return false;
		}
		$this->catinfo=$model->getCatInfo($cat);
		if ($this->catinfo) {
			if (count($this->catinfo) == 1) $this->document->setTitle($this->catinfo[0]->cat_title);
			$artids=$model->getCatArts($cat);
			if (count($artids) > 0) {
				$this->articles=$model->getArticles($artids);
				$this->pagination = $this->get('Pagination');
			}
		}
	}
	
	protected function listAll() {
		$model =& $this->getModel();
		$this->articles=$model->getArticles();
		$this->pagination = $this->get('Pagination');
	}
	
	
	protected function listSection() {
		$model =& $this->getModel();
		$sec=$this->getSecs();
		if (!$sec) {
			JError::raiseError(404, JText::_('COM_MAMS_ARTICLE_NOT_FOUND'));
			$this->error=true;
			return false;
		}
		$this->secinfo=$model->getSecInfo($sec);
		if ($this->secinfo) {
			if (count($this->secinfo) == 1) $this->document->setTitle($this->secinfo[0]->sec_name);
			$artids=$model->getSecArts($sec); 
			$this->articles=$model->getArticles($artids,$sec);
			$this->pagination = $this->get('Pagination');
		}
	}
	
	protected function listAuthor() {
		$model =& $this->getModel();
		$sec=$this->getSecs();
		if (!$sec) {
			JError::raiseError(404, JText::_('COM_MAMS_ARTICLE_NOT_FOUND'));
			$this->error=true;
			return false;
		}
		if (count($sec)) $this->secinfo=$model->getSecInfo($sec);
		$aut=$this->getAuts();
		if (!$aut) {
			JError::raiseError(404, JText::_('COM_MAMS_ARTICLE_NOT_FOUND'));
			$this->error=true;
			return false;
		}
		$this->autinfo=$model->getAutInfo($aut);
		if ($this->autinfo) {
			$artids=$model->getAuthArts($aut);
			$this->articles=$model->getArticles($artids,$sec);
			$this->pagination = $this->get('Pagination');
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
	
	
}
?>
