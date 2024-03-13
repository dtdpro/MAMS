<?php

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
        return MAMSProvider::getCats($args['showCount'], 0,$args['order'],$args['onlyFeatCat'],$args['restrictFeatCat']);
    }
}
