<?php
/**
 * RenderTest plugin for MAMS
 * @license http://www.gnu.org/licenses/gpl.html GNU/GPL.
 * @by DtD Productions
 * @Copyright (C) 2023
  */
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgMAMSRenderTest extends JPlugin
{

	public function onMAMSRenderA($context, &$row, &$params, $page = 0) {
		return '<div class="alert alert-danger uk-alert uk-alert-danger">Render Test<br><strong>'.$row->title.'</strong><br>ID:'.$row->id.'</div>';
	}
	
}
