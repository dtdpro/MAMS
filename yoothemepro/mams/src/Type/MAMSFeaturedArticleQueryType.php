<?php

use YooTheme\Database;

class MAMSFeaturedArticleQueryType
{

    public static function config()
    {
        return [

            'fields' => [

                'mamsfeaturedarticletype' => [

                    'type' => [
                        'listOf' => 'MAMSArticleType',
                    ],

                    'args' => [
                        'restrictFeat' => [
							'type' => 'Boolean',
						],
						'limit' => [
							'type' => 'String',
						],
                    ],

                    'metadata' => [

                        'label' => 'MAMS Featured Articles',
                        'group' => 'MAMS',
                        'fields' => [
                            'restrictFeat' => [
                                'text' => 'Restrict by Featured Access Level',
                                'type' => 'checkbox',
	                            'default' => false
                            ],
                            'limit' => [
                                'label' => 'Limit',
                                'type' => 'text',
                                'default' => '5',
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
		return MAMSProvider::getFeaturedArticles($args['limit'],$args['restrictFeat']);
    }
}
