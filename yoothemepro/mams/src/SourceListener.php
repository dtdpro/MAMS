<?php

use YOOtheme\Builder\Source;
use YOOtheme\Config;

class SourceListener
{
    /**
     * @param Source $source
     */
    public static function initSource($source)
    {
        $source->objectType('MAMSCatType', MAMSCatType::config());
	    $source->objectType('MAMSArticleType', MAMSArticleType::config());

        $source->queryType(MAMSCatQueryType::config());
	    $source->queryType(MAMSArticleQueryType::config());
    }

	public static function initCustomizer(Config $config) {
		$config->add(
			'customizer.com_mams.sections',
			array_map(function($section) {
				return ['value'=>$section->sec_id,'text'=>$section->sec_name];
			}, MAMSProvider::secList())
		);
		$config->add(
			'customizer.com_mams.categories',
			array_map(function($section) {
				return ['value'=>$section->cat_id,'text'=>$section->cat_title];
			}, MAMSProvider::catList())
		);
		$config->add(
			'customizer.com_mams.tags',
			array_map(function($section) {
				return ['value'=>$section->tag_id,'text'=>$section->tag_title];
			}, MAMSProvider::tagList())
		);
	}
}
