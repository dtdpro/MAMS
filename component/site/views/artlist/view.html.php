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
			case "allsecs": 
				$this->listAll();
				break;
			case "section": 
			default:
				$this->listSection();
				break;
		}
		
		//RSS Feed Link
		$link = '&format=feed&limitstart=';
		$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
		$this->document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
		$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
		$this->document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		
		parent::display($tpl);
		$this->setLayout('artlist');
		parent::display($tpl);
	}
	
	protected function listCategory() {
		$model =& $this->getModel();
		$sec=$this->getSecs();
		if (count($sec)) $this->secinfo=$model->getSecInfo($sec);
		$cat=$this->getCats();
		$this->catinfo=$model->getCatInfo($cat);
		if ($this->catinfo) {
			$artids=$model->getCatArts($cat);
			$this->articles=$model->getArticles($artids,$sec);
			$this->pagination = $this->get('Pagination');
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
		$this->secinfo=$model->getSecInfo($sec);
		if ($this->secinfo) {
			$artids=$model->getSecArts($sec); 
			$this->articles=$model->getArticles($artids,$sec);
			$this->pagination = $this->get('Pagination');
		}
	}
	
	protected function listAuthor() {
		$model =& $this->getModel();
		$sec=$this->getSecs();
		if (count($sec)) $this->secinfo=$model->getSecInfo($sec);
		$aut=$this->getAuts();
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
