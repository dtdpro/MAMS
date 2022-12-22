<?php
defined('_JEXEC') or die();

use Joomla\Event\Event;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;

jimport( 'joomla.application.component.view');

class MAMSViewArticle extends JViewLegacy
{
	protected $article = null;
	protected $params = null;
	protected $relatedbycat = null;
	protected $relatedbyaut = null;
	
	public function display($tpl = null)
	{
		$layout = $this->getLayout();
		$cfg = MAMSHelper::getConfig();
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		if (JVersion::MAJOR_VERSION == 3)  $dispatcher	= JDispatcher::getInstance();

		$model = $this->getModel();
		$art=$app->input->getInt('artid',0);
		if (!$art) {
			$app->enqueueMessage(JText::_('COM_MAMS_ARTICLE_NOT_FOUND'), 'error');
			$app->setHeader('status', 404, true);
			return false;
		} else {
			$this->article=$model->getArticle($art);
		}
		
		if ($this->article) {
			$this->params = $this->article->params;
			
			$authors = array();
			if ($this->article->auts) {
				foreach ($this->article->auts as $a) {
					$authors[] = $a->auth_fname.(($a->auth_mi) ? " ".$a->auth_mi : "")." ".$a->auth_lname.(($a->auth_titles) ? ", ".$a->auth_titles : "");
				}
			}
			
			$this->document->setMetaData('title', $this->article->art_title);
			
			foreach ($authors as $a) { $this->document->setMetaData('author', $a); }
			
			//Set Metadata Info
			if ($this->article->metadesc) {
				$this->document->setDescription($this->article->metadesc);
			} elseif (!$this->article->metadesc && $this->params->get('menu-meta_description')) {
				$this->document->setDescription($this->params->get('menu-meta_description'));
			}
			
			if ($this->article->metakey) {
				$this->document->setMetadata('keywords', $this->article->metakey);
			} elseif (!$this->article->metakey && $this->params->get('menu-meta_keywords')) {
				$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
			}
			
			if ($this->params->get('robots')) {
				$this->document->setMetadata('robots', $this->params->get('robots'));
			}
			
			//citation meta tags; missing: citation_journal_title, citation_issn, citation_volume, citation_issue, citation_firstpage, citation_lastpage,  citation_publication_date
			$this->document->setMetaData('citation_title', $this->article->art_title);
			foreach ($authors as $a) { $this->document->setMetaData('citation_author', $a); }
			$this->document->setMetaData('citation_online_date', $this->article->art_publish_up);
			
			$mdata = $this->article->metadata->toArray();
			foreach ($mdata as $k => $v) {
				if ($v)	$this->document->setMetadata($k, $v);
			}

			// get return url
			$this->returnUrl = base64_encode(Uri::getInstance());

			if (in_array($this->article->access,$user->getAuthorisedViewLevels())) {
				$this->_prepareTitle();
				$this->article->track_id = MAMSHelper::trackViewed($art,'article');
				if ($this->params->get('show_related',1)) {
					$this->related=$model->getRelated($this->article,$this->article->cats,$this->article->auts,$this->article->sec_id);
				}
				// run mams plugins
				if (JVersion::MAJOR_VERSION == 3) $results = $dispatcher->trigger('onMAMSPrepare', array(&$this->article->art_content));
				else $this->dispatchEvent(new Event('onMAMSPrepare', array(&$this->article->art_content)));

				// run content plugins
				$page_content = (object) array("text" => $this->article->art_content);
				if (JVersion::MAJOR_VERSION == 3) {
					JPluginHelper::importPlugin('content');
					$dispatcher->trigger('onContentPrepare', array ('com_mams.article', &$page_content, &$this->article->params, 0));
				}
				else {
					PluginHelper::importPlugin('content');
					$this->dispatchEvent(new Event('onContentPrepare', array ('com_mams.article', &$page_content, &$this->article->params, 0)));
				}

				// put processed content back
				$this->article->art_content = $page_content->text;

				parent::display($tpl);
			} else {
				if ($user->id) {
					$sec = $model->getArticleSec($art);
					$url = JRoute::_('index.php?option=com_mams&view=artlist&secid='.$sec);
					$msg = $cfg->noaccessmsg;
				}
				else {
					$url = JRoute::_('index.php?option=com_users&view=login&return='.$this->returnUrl);
					$msg = $cfg->loginmsg;
				}
				$app->enqueueMessage($msg, 'message');
				$app->redirect($url);
			}
		} else {
			$app->enqueueMessage(JText::_('COM_MAMS_ARTICLE_NOT_FOUND'), 'error');
			$app->setHeader('status', 404, true);
			return false;
		}
	}

	protected function _prepareTitle()
	{
		$app     = JFactory::getApplication();
		$title   = null;
		$title = $this->article->art_title;
		// Check for empty title and add site name if param is set
		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}
		if (empty($title))
		{
			$title = $this->article->art_title;
		}
		$this->document->setTitle($title);
	}
	
}
?>
