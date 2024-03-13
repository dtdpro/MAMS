<?php

use YooTheme\Database;

class MAMSArticleQueryType
{

    public static function config()
    {
        return [

            'fields' => [

                'mamsarticletype' => [

                    'type' => [
                        'listOf' => 'MAMSArticleType',
                    ],

                    'args' => [
                        'secid' => [
                            'type' => 'String',
                        ],
	                    'catid' => [
		                    'type' => 'String',
	                    ],
	                    'tagid' => [
		                    'type' => 'String',
	                    ],
						'restrictFeat' => [
							'type' => 'Boolean',
						],
						'orderby1' => [
                            'type' => 'String',
                        ],
						'orderby2' => [
							'type' => 'String',
						],
						'orderby3' => [
							'type' => 'String',
						],
						'limit' => [
							'type' => 'String',
						],
                    ],

                    'metadata' => [

                        'label' => 'MAMS Articles',
                        'group' => 'MAMS',
                        'fields' => [
                            'secid' => [
                                'label' => 'Filter by Section IDs',
                                'type' => 'text',
                                'default' => '',
                                "description"=> "Seprate multiple sections by a comma, no spaces.",
                                /*'options' => [['value'=>'','text'=>''],['evaluate'=>'yootheme.builder.mams_sections']],
                                'attrs' => [
                                    'multiple' => true,
                                    'class' => 'uk-height-small',
                                ]*/
                            ],
							'catid' => [
								'label' => 'Filter by Category IDs',
								'type' => 'text',
								'default' => '',
                                "description"=> "Seprate multiple categories by a comma, no spaces."
								/*'options' => [['value'=>'','text'=>''],['evaluate'=>'yootheme.builder.mams_categories']],
                                    'attrs' => [
                                        'multiple' => true,
                                        'class' => 'uk-height-small',
                                    ]*/
                                ],
							'tagid' => [
								'label' => 'Filter by Tag IDs',
								'type' => 'text',
								'default' => '',
                                "description"=> "Seprate multiple tags by a comma, no spaces."
								//'options' => [['value'=>'','text'=>''],['evaluate'=>'yootheme.builder.mams_tags']]
							],
                            'orderby1' => [
                                'label' => 'Order First',
                                'type' => 'select',
                                'default' => 'a.art_publish_up DESC',
                                'options' => [
                                    ['value' => 'a.art_publish_up ASC', 'text' => 'Oldest First'],
	                                ['value' => 'a.art_publish_up DESC', 'text' => 'Newest First'],
                                    ['value' => 'a.art_title ASC', 'text' => 'Title A-Z'],
	                                ['value' => 'a.art_title DESC', 'text' => 'Title Z-A'],
                                    ['value' => 'a.ordering ASC', 'text' => 'Article Order 1-X'],
	                                ['value' => 'a.ordering DESC', 'text' => 'Article Order X-1'],
                                    ['value' => 's.lft ASC', 'text' => 'Section Order 1-X'],
	                                ['value' => 's.lft DESC', 'text' => 'Section Order X-1']
                                ],
                            ],
                            'orderby2' => [
	                            'label' => 'Order Second',
	                            'type' => 'select',
	                            'default' => 's.lft ASC',
	                            'options' => [
		                            ['value' => 'a.art_publish_up ASC', 'text' => 'Oldest First'],
		                            ['value' => 'a.art_publish_up DESC', 'text' => 'Newest First'],
		                            ['value' => 'a.art_title ASC', 'text' => 'Title A-Z'],
		                            ['value' => 'a.art_title DESC', 'text' => 'Title Z-A'],
		                            ['value' => 'a.ordering ASC', 'text' => 'Article Order 1-X'],
		                            ['value' => 'a.ordering DESC', 'text' => 'Article Order X-1'],
		                            ['value' => 's.lft ASC', 'text' => 'Section Order 1-X'],
		                            ['value' => 's.lft DESC', 'text' => 'Section Order X-1']
	                            ],
                            ],
                            'orderby3' => [
	                            'label' => 'Order Third',
	                            'type' => 'select',
	                            'default' => 'a.ordering ASC',
	                            'options' => [
		                            ['value' => 'a.art_publish_up ASC', 'text' => 'Oldest First'],
		                            ['value' => 'a.art_publish_up DESC', 'text' => 'Newest First'],
		                            ['value' => 'a.art_title ASC', 'text' => 'Title A-Z'],
		                            ['value' => 'a.art_title DESC', 'text' => 'Title Z-A'],
		                            ['value' => 'a.ordering ASC', 'text' => 'Article Order 1-X'],
		                            ['value' => 'a.ordering DESC', 'text' => 'Article Order X-1'],
		                            ['value' => 's.lft ASC', 'text' => 'Section Order 1-X'],
		                            ['value' => 's.lft DESC', 'text' => 'Section Order X-1']
	                            ],
                            ],
	                        'settingsLabel' => [
		                        'label' => 'Settings',
		                        'type' => 'label'
	                        ],
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
		return MAMSProvider::getArticles($args['secid'],$args['catid'],$args['tagid'],$args['limit'],$args['orderby1'],$args['orderby2'],$args['orderby2'],$args['restrictFeat']);
    }
}
