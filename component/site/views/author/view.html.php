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
		$this->_prepareTitle();
		if ($err) parent::display($tpl);
		else return false;
	}
	
	protected function showAuthor($aut) {
		$cfg = MAMSHelper::getConfig();
		$model = $this->getModel();
		$this->author=$model->getAuthor($aut);
		if ($this->author) {
			MAMSHelper::trackViewed($aut,'author');
			if ($this->params->get('show_pubed',1)) { 
				 $this->published=$model->getPublishedItems($aut,$this->params);
				 //$this->courses=$model->getAuthCourses($aut);
			}
			return true;
		} else {
			return JError::raiseError(404, JText::_('COM_MAMS_AUTHOR_NOT_FOUND'));
		}
	}
	
	protected function listAuthors() {
		MAMSHelper::trackViewed(0,'authors');
		$model = $this->getModel();
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

	protected function _prepareTitle()
	{
		$app     = JFactory::getApplication();
		$title   = null;

		$title = $this->author->auth_name;
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
			$title = $this->author->auth_name;
		}
		$this->document->setTitle($title);
	}
	
}
?>
