<?php
defined('_JEXEC') or die();
/**
 * @version		$Id: view.html.php 2012-01-13 $
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
		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		
		$model =& $this->getModel();
		$art=JRequest::getInt('artid',0);
		$this->article=$model->getArticle($art);
		if ($this->article) {
			$this->document->setTitle($this->article->art_title);
			MAMSHelper::trackViewed($art,'article');
			if ($this->article->art_show_related) {
				$this->relatedbycat=$model->getRelatedByCat($art,$this->article->cats,$this->article->sec_id);
				$this->relatedbyaut=$model->getRelatedByAut($art,$this->article->auts,$this->article->sec_id);
			}
			parent::display($tpl);
		} else {
			JError::raiseError(404, JText::_('COM_MAMS_ARTICLE_NOT_FOUND'));
			return false;
		}
	}
	
}
?>
