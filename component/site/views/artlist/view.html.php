<?php
defined('_JEXEC') or die();

use Joomla\Event\Event;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;

jimport( 'joomla.application.component.view');

class MAMSViewArtList extends JViewLegacy
{
	protected $artlist = Array();
	protected $children = Array();
	public $secinfo = null;
	protected $autinfo = null;
	public $catinfo = null;
	protected $params = null;
	protected $pagination = null;
	protected $state = null;
	protected $error = false;
	protected $title='';
	protected $headerContent = false;
	protected $footerContent = false;
	
	public function display($tpl = null)
	{
		$layout = $this->getLayout();
		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		$session = JFactory::getSession();
		$session->set('MAMSLoadfList',true);
		
		$this->state = $this->get('State');
		
		switch($layout) {
			case "tag":
				$this->listTag();
				break;
			case "tagsec":
				$this->listTagSec();
				break;
			case "tagcat":
				$this->listTagCat();
				break;
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
			case "artauthed":
				$this->listArticlesAuthored();
				break;
			case "allsecs": 
				$this->listAll();
				break;
			case "secbycat": 
				$this->listSecbyCat();
				break;
			case "section":
				$this->listSection();
				break;
			default:
				throw new \Exception("Not Found", 404);
				break;
		}
		
		if ($this->error) return false;
		
		// Pre Header and Footer text
		if (JVersion::MAJOR_VERSION == 3)  {
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			if ($this->params->get("extras_header","")) {
				$this->headerContent = $this->params->get("extras_header");
				$headerContent = (object) array("text" => $this->headerContent);
				$dispatcher->trigger('onContentPrepare', array ('com_mams.article', &$headerContent, &$this->params, 0));
				$this->headerContent = $headerContent->text;
			}
			if ($this->params->get("extras_footer","")) {
				$this->footerContent = $this->params->get("extras_footer");
				$footerContent = (object) array("text" => $this->footerContent);
				$dispatcher->trigger('onContentPrepare', array ('com_mams.article', &$footerContent, &$this->params, 0));
				$this->footerContent = $footerContent->text;
			}
		} else {
			PluginHelper::importPlugin('content');
			if ($this->params->get("extras_header","")) {
				$this->headerContent = $this->params->get("extras_header");
				$headerContent = (object) array("text" => $this->headerContent);
				$this->dispatchEvent(new Event('onContentPrepare', array ('com_mams.article', &$headerContent, &$this->params, 0)));
				$this->headerContent = $headerContent->text;
			}
			if ($this->params->get("extras_footer","")) {
				$this->footerContent = $this->params->get("extras_footer");
				$footerContent = (object) array("text" => $this->footerContent);
				$this->dispatchEvent(new Event('onContentPrepare', array ('com_mams.article', &$footerContent, &$this->params, 0)));
				$this->footerContent = $footerContent->text;
			}
		}

		$this->_prepareTitle();
		parent::display($tpl);
		if ($layout != "secbycat" && $layout != "catlist" && $layout != "seclist" && $session->get('MAMSLoadfList')) {
			if ($this->params->get("listview","blog") == "blog") $this->setLayout('artlist');
			else $this->setLayout('artgal');
			parent::display($tpl);
		}

		if ( $this->footerContent ) {
			echo $this->footerContent;
		}

		if ($this->params->get('divwrapper',1)) { echo '</div>'; }
	}
	
	protected function listSecs() {
		$model = $this->getModel();
		$this->seclist = $model->getSecs($this->params->get("show_count",0));
		MAMSHelper::trackViewed(0,'listsecs');
	}
	
	protected function listCats() {
		$model = $this->getModel();
		$this->catlist = $model->getCats($this->params->get("show_count",0));
		MAMSHelper::trackViewed(0,'listcats');
	}
	
	protected function listSecByCat() {
		$model = $this->getModel();
		$sec=$this->getSecs();
		$app = JFactory::getApplication();
		if (!$sec) {
			$app->enqueueMessage(JText::_('COM_MAMS_ARTICLE_NOT_FOUND'), 'error');
			$app->setHeader('status', 404, true);
			return false;
		}
		$this->secinfo=$model->getSecInfo($sec);
		$this->cats = $model->getSecCats($sec);
		if ($this->secinfo && $this->cats) {
			MAMSHelper::trackViewed($this->secinfo[0]->sec_id,'seclist');
			if (count($this->secinfo) == 1) $this->title=$this->secinfo[0]->sec_name;
			foreach ($this->cats as &$c) {
				$cat = array();
				$cat[] = $c->cat_id;
				$artids=$model->getCatArts($cat,true);
				$c->artids = $artids;
				$c->articles=$model->getArticles($artids,$sec,false);
			}
		}
	}
	
	protected function listCatSec() {
		$model = $this->getModel();
		$sec=$this->getSecs();
		$app = JFactory::getApplication();
		if (count($sec)) $this->secinfo=$model->getSecInfo($sec);
		$cat=$this->getCats();
		if (!$cat) {
			throw new \Exception("Not Found", 404);
		}
		$this->catinfo=$model->getCatInfo($cat);
		if ($this->catinfo) {
			MAMSHelper::trackViewed($this->catinfo[0]->cat_id,'catlist');
			if (count($this->catinfo) == 1) $this->title = $this->catinfo[0]->cat_title;
			$artids=$model->getCatArts($cat);
			$this->articles=$model->getArticles($artids,$sec);
			$this->pagination = $this->get('Pagination');
		} else {
			throw new \Exception("Not Found", 404);
		}
	}

	protected function listTagSec() {
		$model = $this->getModel();
		$sec=$this->getSecs();
		$app = JFactory::getApplication();
		if (count($sec)) $this->secinfo=$model->getSecInfo($sec);
		$tag=$this->getTags();
		if (!$tag) {
			throw new \Exception("Not Found", 404);
		}
		$this->taginfo=$model->getTagInfo($tag);
		if ($this->taginfo) {
			MAMSHelper::trackViewed($this->taginfo[0]->tag_id,'taglist');
			if (count($this->taginfo) == 1) $this->title = $this->taginfo[0]->tag_title;
			$artids=$model->getTagArts($tag);
			$this->articles=$model->getArticles($artids,$sec);
			$this->pagination = $this->get('Pagination');
		} else {
			throw new \Exception("Not Found", 404);
		}
	}

	protected function listTagCat() {
		$model = $this->getModel();
		$cat=$this->getCats();
		$app = JFactory::getApplication();
		if (count($cat)) $this->catinfo=$model->getCatInfo($cat);
		$tag=$this->getTags();
		if (!$tag) {
			throw new \Exception("Not Found", 404);
		}
		$this->taginfo=$model->getTagInfo($tag);
		if ($this->taginfo) {
			MAMSHelper::trackViewed($this->taginfo[0]->tag_id,'taglist');
			if (count($this->taginfo) == 1) $this->title = $this->taginfo[0]->tag_title;
			$artids_tag=$model->getTagArts($tag);
			$artids_cat=$model->getCatArts($cat);
			$this->articles=$model->getArticles(array_intersect($artids_tag,$artids_cat));
			$this->pagination = $this->get('Pagination');
		} else {
			throw new \Exception("Not Found", 404);
		}
	}
	
	protected function listCategory() {
		$model = $this->getModel();
		$cat=$this->getCats();
		$app = JFactory::getApplication();
		if (!$cat) {
			throw new \Exception("Not Found", 404);
		}
		$this->catinfo=$model->getCatInfo($cat);
		$this->childcatlist = $model->getCats($this->params->get("show_count",0),$cat);
		if ($this->catinfo) {
			MAMSHelper::trackViewed($this->catinfo[0]->cat_id,'catlist');
			if (count($this->catinfo) == 1) $this->title = $this->catinfo[0]->cat_title;
			$artids=$model->getCatArts($cat);
			if (count($artids) > 0) {
				$this->articles=$model->getArticles($artids);
				$this->pagination = $this->get('Pagination');
			}
		}
	}

	protected function listTag() {
		$model = $this->getModel();
		$tag=$this->getTags();
		$app = JFactory::getApplication();
		if (!$tag) {
			throw new \Exception("Not Found", 404);
		}
		$this->taginfo=$model->getTagInfo($tag);
		if ($this->taginfo) {
			MAMSHelper::trackViewed($this->taginfo[0]->tag_id,'taglist');
			if (count($this->taginfo) == 1) $this->title = $this->taginfo[0]->tag_title;
			$artids=$model->getTagArts($tag);
			if (count($artids) > 0) {
				$this->articles=$model->getArticles($artids);
				$this->pagination = $this->get('Pagination');
			}
		} else {
			throw new \Exception("Not Found", 404);
		}
	}
	
	protected function listAll() {
		$model = $this->getModel();
		$this->articles=$model->getArticles();
		$this->pagination = $this->get('Pagination');
		MAMSHelper::trackViewed(0,'listarts');
	}
	
	
	protected function listSection() {
		$model = $this->getModel();
		$sec=$this->getSecs();
		$app = JFactory::getApplication();
		if (!$sec) {
			throw new \Exception("Not Found", 404);
		}
		$this->secinfo=$model->getSecInfo($sec);
		if ($this->secinfo) {
			MAMSHelper::trackViewed($this->secinfo[0]->sec_id,'seclist');
			if (count($this->secinfo) == 1) $this->title = $this->secinfo[0]->sec_name;
			$artids=$model->getSecArts($sec);
			$this->children=$model->getSecChildren($sec);
			$this->articles=$model->getArticles($artids,$sec);
			$this->pagination = $this->get('Pagination');
		} else {
			throw new \Exception("Not Found", 404);
		}
	}
	
	protected function listAuthor() {
		$model = $this->getModel();
		$sec=$this->getSecs();
		$app = JFactory::getApplication();
		if (!$sec) {
			throw new \Exception("Not Found", 404);
		}
		if (count($sec)) $this->secinfo=$model->getSecInfo($sec);
		$aut=$this->getAuts();
		if (!$aut) {
			throw new \Exception("Not Found", 404);
		}
		$this->autinfo=$model->getAutInfo($aut);
		if ($this->autinfo) {
			MAMSHelper::trackViewed($this->autinfo[0]->auth_id,'autlist');
			$artids=$model->getAuthArts($aut);
			$this->articles=$model->getArticles($artids,$sec);
			$this->pagination = $this->get('Pagination');
		} else {
			throw new \Exception("Not Found", 404);
		}
	}

	protected function listArticlesAuthored() {
		$model = $this->getModel();
		$app = JFactory::getApplication();
		$input = $app->input;
		$artid=$input->get('artid',0,'INT');
		if (!$artid) {
			throw new \Exception("Not Found", 404);
		}
		$this->authors = $model->getArticlesAuthoredAuthors($artid);
		$authids = [];
		foreach ($this->authors as $auth) {
			$authids[] = $auth->auth_id;
		}
		$artids = $model->getArticlesAuthored($authids,$artid);
		if (count($artids) !== 0) {
			$this->articles   = $model->getArticles( $artids );
			$this->pagination = $this->get( 'Pagination' );
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

	protected function getTags() {
		$tags = array();
		foreach (JFactory::getApplication()->input->get('tagid', array(), 'array') as $t) {
			if ((int)$t) $tags[] = (int)$t;
		}
		return $tags;
	}
	
	protected function getAuts() {
		$auts = array();
		foreach (JFactory::getApplication()->input->get('autid', array(), 'array') as $a) {
			if ((int)$a) $auts[] = (int)$a;
		}
		return $auts;
	}

	protected function _prepareTitle()
	{
		$app     = JFactory::getApplication();
		$menus   = $app->getMenu();
		$title   = null;
		$params = $this->params;
		$title = $this->title;
		// Check for empty title and add site name if param is set
		if (empty($title))
		{
			$title = $this->params->get('page_title', '');
		}
		if ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}
		if (empty($title))
		{
			$title = $this->title;
		}
		$this->document->setTitle($title);
	}
	
}
?>
