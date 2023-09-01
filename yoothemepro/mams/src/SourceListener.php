<?php

use YOOtheme\Builder\Source;
use YOOtheme\Builder\BuilderConfig;

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

	public static function initCustomizer(BuilderConfig $config) {
		$config->merge([
			'mams_sections'=>
			array_map(function($section) {
				return ['value'=>$section->sec_id,'text'=>$section->sec_name];
			}, MAMSProvider::secList()),
            'mams_categories' =>
            array_map(function($section) {
                return ['value'=>$section->cat_id,'text'=>$section->cat_title];
            }, MAMSProvider::catList()),
            'mams_tags' =>
            array_map(function($section) {
                return ['value'=>$section->tag_id,'text'=>$section->tag_title];
            }, MAMSProvider::tagList())
		]);
	}
}
