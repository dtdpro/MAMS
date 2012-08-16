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
class MAMSViewAuthor extends JView
{
	protected $author = null;
	protected $params = null;
	protected $published = null;
	protected $autlist = null;
	
	public function display($tpl = null)
	{
		$layout = $this->getLayout();
		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		$aut=JRequest::getInt('autid',0);
		
		if ($aut) { $layout="default"; $this->setLayout('default;'); }
		
		switch($layout) {
			case "default": 
				$err=$this->showAuthor();
				break;
			case "list": 
				$err=$this->listAuthors();
				break;
		}
		if ($err) parent::display($tpl);
		else return false;
	}
	
	protected function showAuthor() {
		$cfg = MAMSHelper::getConfig();
		$model =& $this->getModel();
		$aut=JRequest::getInt('autid',0);
		$this->author=$model->getAuthor($aut);
		if ($this->author) {
			MAMSHelper::trackViewed($aut,'author');
			$this->document->setTitle($this->author->auth_name);
			if ($this->params->get('show_pubed',1)) { 
				 $this->published=$model->getPublished($aut);
				 $this->courses=$model->getAuthCourses($aut);
			}
			return true;
		} else {
			return JError::raiseError(404, JText::_('COM_MAMS_AUTHOR_NOT_FOUND'));
		}
	}
	
	protected function listAuthors() {
		MAMSHelper::trackViewed(0,'authors');
		$model =& $this->getModel();
		$this->autlist = $model->getAuthorList($this->params->get("secid",0)); 
		return true;
	}
	
}
?>
