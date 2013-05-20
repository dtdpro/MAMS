<?php

defined('_JEXEC') or die;

require_once dirname(__FILE__).'/medias.php';


class MAMSControllerFeaturedMedia extends MAMSControllerMedias
{
	

	public function getModel($name = 'FeatureMedia', $prefix = 'MAMSModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}