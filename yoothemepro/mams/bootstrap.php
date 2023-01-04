<?php

use YOOtheme\Builder;
use YOOtheme\Path;

include_once __DIR__ . '/src/SourceListener.php';
include_once __DIR__ . '/src/MAMSProvider.php';
include_once __DIR__ . '/src/Type/MAMSCatType.php';
include_once __DIR__ . '/src/Type/MAMSCatQueryType.php';

return [

    'events' => [
        'source.init' => [
            SourceListener::class => 'initSource',
        ],

    ],

];
