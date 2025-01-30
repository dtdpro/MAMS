<?php

use mams\src\MAMSMatchTemplate;
use mams\src\MAMSSourceListener;
use YOOtheme\Builder;
use YOOtheme\Builder\BuilderConfig;
use YOOtheme\Path;

include_once __DIR__ . '/src/MAMSSourceListener.php';
include_once __DIR__ . '/src/MAMSMatchTemplate.php';
include_once __DIR__ . '/src/MAMSProvider.php';
include_once __DIR__ . '/src/Type/MAMSArticleType.php';
include_once __DIR__ . '/src/Type/MAMSAuthorType.php';
include_once __DIR__ . '/src/Type/MAMSCatType.php';
include_once __DIR__ . '/src/Type/MAMSDownloadType.php';
include_once __DIR__ . '/src/Type/MAMSLinkType.php';
include_once __DIR__ . '/src/Type/MAMSSectionType.php';
include_once __DIR__ . '/src/Type/MAMSCategoryType.php';
include_once __DIR__ . '/src/Type/MAMSArticleQueryType.php';
include_once __DIR__ . '/src/Type/MAMSFeaturedArticleQueryType.php';
include_once __DIR__ . '/src/Type/MAMSCatQueryType.php';
include_once __DIR__ . '/src/Type/MAMSPageArticlesQueryType.php';
include_once __DIR__ . '/src/Type/MAMSPageSectionQueryType.php';
include_once __DIR__ . '/src/Type/MAMSPageCategoryQueryType.php';

return [

    'events' => [
        'source.init' => [
            MAMSSourceListener::class => 'initSource',
        ],
        'builder.template' => [MAMSMatchTemplate::class => '@handle'],
        /*'customizer.init' => [
	        SourceListener::class => ['initCustomizer',10],
        ],*/
        BuilderConfig::class => [MAMSSourceListener::class => '@initCustomizer']

    ],

];
