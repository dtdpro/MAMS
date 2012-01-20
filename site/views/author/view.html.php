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
		
		switch($layout) {
			case "default": 
				$this->showAuthor();
				break;
			case "list": 
				$this->listAuthors();
				break;
		}
		parent::display($tpl);
	}
	
	protected function showAuthor() {
		$model =& $this->getModel();
		$aut=JRequest::getInt('autid',0);
		$this->author=$model->getAuthor($aut);
		if ($this->author) {
			$this->document->setTitle($this->author->auth_name);
			if ($this->params->get('show_pubed',1)) $this->published=$model->getPublished($aut);
			
		} 
	}
	
	protected function listAuthors() {
		$model =& $this->getModel();
		$this->autlist = $model->getAuthorList(); 
	}
	
}
?>
