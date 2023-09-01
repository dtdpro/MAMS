<?php

use YOOtheme\Builder;
use YOOtheme\Path;
use YOOtheme\Builder\BuilderConfig;

include_once __DIR__ . '/src/SourceListener.php';
include_once __DIR__ . '/src/MAMSProvider.php';
include_once __DIR__ . '/src/Type/MAMSCatType.php';
include_once __DIR__ . '/src/Type/MAMSCatQueryType.php';
include_once __DIR__ . '/src/Type/MAMSArticleType.php';
include_once __DIR__ . '/src/Type/MAMSArticleQueryType.php';

return [

    'events' => [
        'source.init' => [
            SourceListener::class => 'initSource',
        ],
        /*'customizer.init' => [
	        SourceListener::class => ['initCustomizer',10],
        ],*/
        //BuilderConfig::class => [SourceListener::class => '@initCustomizer']

    ],

];
