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
	
	public function display($tpl = null)
	{
		$layout = $this->getLayout();
		
		switch($layout) {
			case "category": 
				$this->listCat();
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
		$cat=Jrequest::getInt('cat');
		$this->catinfo=$model->getCatInfo($cat);
		$artids=$model->getCatArts($cat);
		$this->articles=$model->getArticles($artids);
	}
	
	protected function listSection() {
		$model =& $this->getModel();
		$sec=1; //Jrequest::getInt('sec');
		$this->secinfo=$model->getSecInfo($sec);
		if ($this->secinfo) {
			$artids=$model->getSecArts($sec);
			$this->articles=$model->getArticles($artids);
		}
	}
	
	protected function listAuthor() {
		$model =& $this->getModel();
		$aut=Jrequest::getInt('aut');
		$this->autinfo=$model->getAutInfo($aut);
		$artids=$model->getAutArts($aut);
		$this->articles=$model->getArticles($artids);
	}
	
	
}
?>
