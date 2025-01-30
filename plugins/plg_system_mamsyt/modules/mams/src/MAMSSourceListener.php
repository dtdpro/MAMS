<?php

namespace mams\src;

use mams\src\Type\MAMSArticleQueryType;
use mams\src\Type\MAMSArticleType;
use mams\src\Type\MAMSAuthorType;
use mams\src\Type\MAMSCategoryType;
use mams\src\Type\MAMSCatQueryType;
use mams\src\Type\MAMSCatType;
use mams\src\Type\MAMSDownloadType;
use mams\src\Type\MAMSFeaturedArticleQueryType;
use mams\src\Type\MAMSLinkType;
use mams\src\Type\MAMSPageArticlesQueryType;
use mams\src\Type\MAMSPageCategoryQueryType;
use mams\src\Type\MAMSPageSectionQueryType;
use mams\src\Type\MAMSSectionType;
use YOOtheme\Builder\BuilderConfig;
use YOOtheme\Builder\Source;

class MAMSSourceListener
{
    /**
     * @param Source $source
     */
    public static function initSource($source)
    {
        $source->objectType('MAMSCatType', MAMSCatType::config());
        $source->objectType('MAMSArticleType', MAMSArticleType::config());
        $source->objectType('MAMSDownloadType', MAMSDownloadType::config());
        $source->objectType('MAMSSectionType', MAMSSectionType::config());
        $source->objectType('MAMSCategoryType', MAMSCategoryType::config());
        $source->objectType('MAMSAuthorType', MAMSAuthorType::config());
        $source->objectType('MAMSLinkType', MAMSLinkType::config());

        $source->queryType(MAMSCatQueryType::config());
        $source->queryType(MAMSFeaturedArticleQueryType::config());
        $source->queryType(MAMSArticleQueryType::config());
        $source->queryType(MAMSPageArticlesQueryType::config());
        $source->queryType(MAMSPageSectionQueryType::config());
        $source->queryType(MAMSPageCategoryQueryType::config());
    }

    public static function initCustomizer(BuilderConfig $config)
    {
        $templates = [
            'com_mams.artlist' => [
                'label' => 'MAMS Article List',
                'fieldset' => [
                    'default' => [
                        'fields' => [
                            'secid' => ($section = [
                                'label' => 'Limit by Section',
                                'description' => 'The template is only assigned to article lists from the selected sections.',
                                'type' => 'select',
                                'default' => [],
                                'options' => [['value' => '', 'text' => ''], ['evaluate' => 'yootheme.builder.mams_sections']],
                                'required' => true,
                                'attrs' => [
                                    'multiple' => true,
                                    'class' => 'uk-height-small',
                                ],
                            ]),
                            'catid' => ($category = [
                                'label' => 'Limit by Category',
                                'description' => 'The template is only assigned to article lists from the selected categories.',
                                'type' => 'select',
                                'default' => [],
                                'options' => [['value' => '', 'text' => ''], ['evaluate' => 'yootheme.builder.mams_categories']],
                                'required' => true,
                                'attrs' => [
                                    'multiple' => true,
                                    'class' => 'uk-height-small',
                                ],
                            ])
                        ],
                    ],
                ],
            ],
        ];


        $config->merge([
            'templates' => $templates,
            'mams_sections' =>
                array_map(function ($section) {
                    return ['value' => $section->sec_id, 'text' => $section->sec_name];
                }, MAMSProvider::secList()),
            'mams_categories' =>
                array_map(function ($section) {
                    return ['value' => $section->cat_id, 'text' => $section->cat_title];
                }, MAMSProvider::catList()),
            'mams_tags' =>
                array_map(function ($section) {
                    return ['value' => $section->tag_id, 'text' => $section->tag_title];
                }, MAMSProvider::tagList())
        ]);
    }
}
