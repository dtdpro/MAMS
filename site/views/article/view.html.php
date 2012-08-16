<?php
defined('_JEXEC') or die();
/**
 * @version		$Id: view.html.php 2012-05-30 $
 * @package		MAMS.Site
 * @subpackage	article
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

jimport( 'joomla.application.component.view');

/**
 * MAMS Article View
 *
 * @static
 * @package		MAMS.Site
 * @subpackage	article
 * @since		1.0
 */
class MAMSViewArticle extends JView
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
		$this->params = $app->getParams();
		
		$model =& $this->getModel();
		$art=JRequest::getInt('artid',0);
		$this->article=$model->getArticle($art);
		
		if ($this->article) {
			if (in_array($this->article->access,$user->getAuthorisedViewLevels())) {
				$this->document->setTitle($this->article->art_title);
				MAMSHelper::trackViewed($art,'article');
				if ($this->article->art_show_related) {
					$this->relatedbycat=$model->getRelatedByCat($art,$this->article->cats,$this->article->sec_id);
					$this->relatedbyaut=$model->getRelatedByAut($art,$this->article->auts,$this->article->sec_id);
				}
				//run plugins
				$results = $dispatcher->trigger('onMAMSPrepare', array(&$this->article->art_content));
				parent::display($tpl);
			} else {
				$urlnc = $this->getReturnURL();
				if ($cfg->continued) $url = JRoute::_('index.php?option=com_continued&view=login&layout=login&return='.$urlnc);
				else if ($cfg->mue) $url = JRoute::_('index.php?option=com_mue&view=login&layout=login&return='.$urlnc);
				else $url = JRoute::_('index.php?option=com_users&view=login&return='.$urlnc);
				$app->redirect($url,$cfg->loginmsg);
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
