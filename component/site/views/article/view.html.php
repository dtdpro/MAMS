<?php
defined('_JEXEC') or die();


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
		$dispatcher	= JDispatcher::getInstance();
		

		
		$model =& $this->getModel();
		$art=JRequest::getInt('artid',0);
		$this->article=$model->getArticle($art);
		
		if ($this->article) {
			$this->params = $this->article->params;
			
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
			
			$mdata = $this->article->metadata->toArray();
			foreach ($mdata as $k => $v) {
				if ($v)	$this->document->setMetadata($k, $v);
			}
			
			if ($this->article->auts) {	
				$a = $this->article->auts[0];
				$this->document->setMetaData('author', $a->auth_fname.(($a->auth_mi) ? " ".$a->auth_mi : "")." ".$a->auth_lname.(($a->auth_titles) ? ", ".$a->auth_titles : ""));
			}
			
			if (in_array($this->article->access,$user->getAuthorisedViewLevels())) {
				$this->document->setTitle($this->article->art_title);
				MAMSHelper::trackViewed($art,'article');
				if ($this->params->get('show_related',1)) {
					$this->relatedbycat=$model->getRelatedByCat($art,$this->article->cats,$this->article->sec_id);
					$this->relatedbyaut=$model->getRelatedByAut($art,$this->article->auts,$this->article->sec_id);
				}
				//run plugins
				$results = $dispatcher->trigger('onMAMSPrepare', array(&$this->article->art_content));
				parent::display($tpl);
			} else {
				$urlnc = $this->getReturnURL();
				if ($user->id) {
					$sec = $model->getArticleSec($art);
					$url = JRoute::_('index.php?option=com_mams&view=artlist&secid='.$sec);
					$msg = $cfg->noaccessmsg;
				}
				else {
					$url = JRoute::_('index.php?option=com_users&view=login&return='.$urlnc);
					$msg = $cfg->loginmsg;
				}
				$app->redirect($url,$msg);
			}
		} else {
			JError::raiseError(404, JText::_('COM_MAMS_ARTICLE_NOT_FOUND'));
			return false;
		}
	}
	
	static function getReturnURL()
	{
		$app	= JFactory::getApplication();
		$router = $app->getRouter();
		$url = null;
		
		// stay on the same page
		$uri = clone JFactory::getURI();
		$vars = $router->parse($uri);
		unset($vars['lang']);
		if ($router->getMode() == JROUTER_MODE_SEF)
		{
			if (isset($vars['Itemid']))
			{
				$itemid = $vars['Itemid'];
				$menu = $app->getMenu();
				$item = $menu->getItem($itemid);
				unset($vars['Itemid']);
				if (isset($item) && $vars == $item->query) {
					$url = 'index.php?Itemid='.$itemid;
				}
				else {
					$url = 'index.php?'.JURI::buildQuery($vars).'&Itemid='.$itemid;
				}
			}
			else
			{
				$url = 'index.php?'.JURI::buildQuery($vars);
			}
		}
		else
		{
			$url = 'index.php?'.JURI::buildQuery($vars);
		}
		
	
		return base64_encode($url);
	}
	
}
?>
