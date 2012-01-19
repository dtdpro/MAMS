<?php
defined('_JEXEC') or die();
/**
 * @version		$Id: view.html.php 2012-01-13 $
 * @package		MAMS.Site
 * @subpackage	artlist
 * @copyright	Copyright (C) 2012 Corona Productions.
 * @license		GNU General Public License version 2
 */

jimport( 'joomla.application.component.view');

/**
 * MAMS Article List View
 *
 * @static
 * @package		MAMS.Site
 * @subpackage	artlist
 * @since		1.0
 */
class MAMSViewArtList extends JView
{
	protected $artlist = Array();
	protected $secinfo = null;
	protected $autinfo = null;
	protected $catinfo = null;
	protected $params = null;
	
	public function display($tpl = null)
	{
		$layout = $this->getLayout();
		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		
		switch($layout) {
			case "category": 
				$this->listCategory();
				break;
			case "section": 
				$this->listSection();
				break;
			case "author": 
				$this->listAuthor();
				break;
		}
		parent::display($tpl);
		$this->setLayout('artlist');
		parent::display($tpl);
	}
	
	protected function listCategory() {
		$model =& $this->getModel();
		$sec=Jrequest::getInt('secid',1);
		$this->secinfo=$model->getSecInfo($sec);
		$cat=Jrequest::getInt('catid');
		$this->catinfo=$model->getCatInfo($cat);
		if ($this->catinfo) {
			$artids=$model->getCatArts($cat);
			$this->articles=$model->getArticles($artids);
		}
	}
	
	protected function listSection() {
		$model =& $this->getModel();
		$sec=Jrequest::getInt('secid',1);
		$this->secinfo=$model->getSecInfo($sec);
		if ($this->secinfo) {
			$artids=$model->getSecArts($sec);
			$this->articles=$model->getArticles($artids);
		}
	}
	
	protected function listAuthor() {
		$model =& $this->getModel();
		$sec=Jrequest::getInt('secid',1);
		if ($sec) $this->secinfo=$model->getSecInfo($sec);
		$aut=Jrequest::getInt('autid');
		$this->autinfo=$model->getAutInfo($aut);
		if ($this->autinfo) {
			$artids=$model->getAuthArts($aut);
			$this->articles=$model->getArticles($artids);
		}
	}
	
	
}
?>
