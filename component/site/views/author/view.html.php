<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class MAMSViewAuthor extends JViewLegacy
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
		$aut=$app->input->getInt('autid',0);
		
		if ($aut) { $layout="default"; $this->setLayout('default;'); }
		
		switch($layout) {
			case "default": 
				$err=$this->showAuthor($aut);
				break;
			case "list": 
				$err=$this->listAuthors();
				break;
		}
		if ($err) parent::display($tpl);
		else return false;
	}
	
	protected function showAuthor($aut) {
		$cfg = MAMSHelper::getConfig();
		$model =& $this->getModel();
		$this->author=$model->getAuthor($aut);
		if ($this->author) {
			MAMSHelper::trackViewed($aut,'author');
			$this->document->setTitle($this->author->auth_name);
			if ($this->params->get('show_pubed',1)) { 
				 $this->published=$model->getPublished($aut);
				 //$this->courses=$model->getAuthCourses($aut);
			}
			return true;
		} else {
			return JError::raiseError(404, JText::_('COM_MAMS_AUTHOR_NOT_FOUND'));
		}
	}
	
	protected function listAuthors() {
		MAMSHelper::trackViewed(0,'authors');
		$model =& $this->getModel();
		$this->autlist = $model->getAuthorList($this->getSecs()); 
		return true;
	}
	
	protected function getSecs() {
		$secs = array();
		foreach (JFactory::getApplication()->input->get('secid', array(), 'array') as $s) {
			if ((int)$s) $secs[] = (int)$s;
		}
		return $secs;
	}
	
}
?>
