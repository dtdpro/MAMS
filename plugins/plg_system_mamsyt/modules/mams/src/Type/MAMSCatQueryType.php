<?php

namespace mams\src\Type;

use mams\src\MAMSProvider;
use YooTheme\Database;

class MAMSCatQueryType
{

    public static function config()
    {
        return [

            'fields' => [

                'mamscattype' => [

                    'type' => [
                        'listOf' => 'MAMSCatType',
                    ],

                    'args' => [
                        'parent' => [
                            'type' => 'String',
                        ],
                        'onlyFeatCat' => [
                            'type' => 'Boolean',
                        ],
                        'restrictFeatCat' => [
                            'type' => 'Boolean',
                        ],
                        'showCount' => [
                            'type' => 'Boolean',
                        ],
                        'order' => [
                            'type' => 'String',
                        ],
                    ],

                    'metadata' => [

                        'label' => 'MAMS Categories',
                        'group' => 'MAMS',
                        'fields' => [
                            'parent' => [
                                'label' => 'Parent Category ID',
                                'type' => 'text',
                                'default' => '0',
                                "description" => "Parent category ID, 0 for root/none."
                                /*'options' => [['value'=>'','text'=>''],['evaluate'=>'yootheme.builder.mams_categories']],
                                    'attrs' => [
                                        'multiple' => true,
                                        'class' => 'uk-height-small',
                                    ]*/
                            ],
                            'order' => [
                                'label' => 'Ordering',
                                'type' => 'select',
                                'default' => 'titasc',
                                'options' => [
                                    ['value' => 'titasc', 'text' => 'Title A-Z'],
                                    ['value' => 'titdsc', 'text' => 'Title Z-A'],
                                    ['value' => 'orderasc', 'text' => 'Ordering Ascending'],
                                    ['value' => 'orderdsc', 'text' => 'Ordering Descending'],
                                ],
                            ],
                            'onlyFeatCat' => [
                                'text' => 'Show Only Featured Catagories',
                                'type' => 'checkbox',
                            ],
                            'restrictFeatCat' => [
                                'text' => 'Featured Access Level Restriction',
                                'type' => 'checkbox',
                                'enable' => 'onlyFeatCat',
                            ],
                            'showCount' => [
                                'text' => 'Include Article Count in Title',
                                'type' => 'checkbox',

                            ],
                        ],

                    ],

                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],

                ],

            ]

        ];
    }

    public static function resolve($item, $args, $context, $info)
    {
        return MAMSProvider::getCats($args['showCount'], $args['parent'], $args['order'], $args['onlyFeatCat'], $args['restrictFeatCat']);
    }
}
