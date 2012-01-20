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
	protected $related = null;
	
	public function display($tpl = null)
	{
		$layout = $this->getLayout();
		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		
		$model =& $this->getModel();
		$art=JRequest::getInt('artid',1);
		$this->article=$model->getArticle($art);
		if ($this->article) {
			$this->document->setTitle($this->article->art_title);
			if ($this->article->art_show_related) $this->related=$model->getRelated($this->article->cats,$this->article->auts,$this->article->sec_id);
			parent::display($tpl);
		}
	}
	
}
?>
