<?php
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
	}
	
	protected function listCategory() {
		$model =& $this->getModel();
		$cat=Jrequest::getInt('cat');
		$catinfo=$model->getCatInfo($cat);
		$artids=$model->getCatArts($cat);
		$articles=$model->getArticles($artids);
		$this->assignRef('articles',$articles);
	}
	
	protected function listSection() {
		$model =& $this->getModel();
		$sec=Jrequest::getInt('sec');
		$secinfo=$model->getSecInfo($sec);
		$artids=$model->getSecArts($sec);
		$articles=$model->getArticles($artids);
		$this->assignRef('articles',$articles);
	}
	
	protected function listAuthor() {
		$model =& $this->getModel();
		$aut=Jrequest::getInt('aut');
		$autinfo=$model->getAutInfo($aut);
		$artids=$model->getAutArts($aut);
		$articles=$model->getArticles($artids);
		$this->assignRef('articles',$articles);
	}
	
	
}
?>
