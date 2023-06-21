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

		// get return url
		$this->returnUrl = base64_encode(Uri::getInstance());

		// Get Article ID
		$art=$app->input->getInt('artid',0);

		// If no article ID, error
		if (!$art) {
			$app->enqueueMessage(JText::_('COM_MAMS_ARTICLE_NOT_FOUND'), 'error');
			$app->setHeader('status', 404, true);
			return false;
		}

		// get access details for article
		$accessDetails = $model->getArticleAccessDetails($art);

		// if articel does not exist, error
		if (!$accessDetails->exists) {
			$app->enqueueMessage(JText::_('COM_MAMS_ARTICLE_NOT_FOUND'), 'error');
			$app->setHeader('status', 404, true);
			return false;
		}

		// check for access and preview
		if ($accessDetails->canAccess) {
			// get article if user can access
			$this->article=$model->getArticle($art);
		} else {
			if ($accessDetails->hasPreview) {
				// get article preview if the article has one
				$this->article=$model->getArticle($art,true);
			} else {
				// if no preview or access, redirect
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


			if (in_array($this->article->access,$user->getAuthorisedViewLevels()) || $accessDetails->hasPreview) {
				$this->_prepareTitle();
				$this->article->track_id = MAMSHelper::trackViewed($art,'article');
				if ($this->params->get('show_related',1)) {
					$this->related=$model->getRelated($this->article,$this->article->cats,$this->article->auts,$this->article->sec_id);
				}

				// run plugins
				$item = (object) [
					"title" => $this->article->art_title,
					"text" => $this->article->art_content,
					'id'=>$this->article->art_id,
					'sec' => $this->article->sec_id,
					'cats' => $this->article->cats
				];
				if (JVersion::MAJOR_VERSION == 3) {
					$results = $dispatcher->trigger('onMAMSPrepare', array(&$item->text));

					$results = $dispatcher->trigger('onMAMSRenderA', array ('com_mams.article', &$item, &$this->article->params, 0));
					$this->article->rendera = trim(implode("\n", $results));

					JPluginHelper::importPlugin('content');
					$dispatcher->trigger('onContentPrepare', array ('com_mams.article', &$item, &$this->article->params, 0));

					$results = $dispatcher->trigger('onContentBeforeDisplay', array ('com_mams.article', &$item, &$this->article->params, 0));
					$this->beforeDisplayContent = trim(implode("\n", $results));

					$results = $dispatcher->trigger('onContentAfterDisplay', array ('com_mams.article', &$item, &$this->article->params, 0));
					$this->afterDisplayContent = trim(implode("\n", $results));
				}
				else {
					$this->dispatchEvent(new Event('onMAMSPrepare', array(&$item->text)));

					$results = $app->triggerEvent('onMAMSRenderA', array ('com_mams.article', &$item, &$this->article->params, 0));
					$this->article->rendera = trim(implode("\n", $results));

					PluginHelper::importPlugin('content');
					$this->dispatchEvent(new Event('onContentPrepare', ['com_mams.article', &$item, &$this->article->params, 0]));

					$results = $app->triggerEvent('onContentBeforeDisplay', ['com_mams.article', &$item, &$this->article->params, 0]);
					$this->beforeDisplayContent = trim(implode("\n", $results));

					$results = $app->triggerEvent('onContentAfterDisplay', ['com_mams.article', &$item, &$this->article->params, 0]);
					$this->afterDisplayContent = trim(implode("\n", $results));
				}

				// put processed content back
				$this->article->art_content = $item->text;

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
