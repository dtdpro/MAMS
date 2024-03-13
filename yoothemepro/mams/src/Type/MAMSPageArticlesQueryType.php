<?php


class MAMSPageArticlesQueryType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'mamsarticles' => [
                    'type' => [
                        'listOf' => 'MAMSArticleType',
                    ],
                    'args' => [
                        'offset' => [
                            'type' => 'Int',
                        ],
                        'limit' => [
                            'type' => 'Int',
                        ],
                    ],
                    'metadata' => [
                        'label' =>'MAMS Articles List',
                        'view' => ['com_mams.artlist'],
                        'group' => 'MAMS',
                        'fields' => [
                            '_offset' => [
                                'description' => 'Set the starting point and limit the number of articles.',
                                'type' => 'grid',
                                'width' => '1-2',
                                'fields' => [
                                    'offset' => [
                                        'label' => 'Start',
                                        'type' => 'number',
                                        'default' => 0,
                                        'modifier' => 1,
                                        'attrs' => [
                                            'min' => 1,
                                            'required' => true,
                                        ],
                                    ],
                                    'limit' => [
                                        'label' => 'Quantity',
                                        'type' => 'limit',
                                        'attrs' => [
                                            'placeholder' => 'No limit',
                                            'min' => 0,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ]
            ],
        ];
    }

    public static function resolve($root, array $args)
    {
        $args += [
            'offset' => 0,
            'limit' => null,
        ];

        if (isset($root['items'])) {
            $items = $root['items'];
            $session = JFactory::getSession();
            $session->set('MAMSLoadfList',false);
            if ($args['offset'] || $args['limit']) {
                $items = array_slice($items, (int) $args['offset'], (int) $args['limit'] ?: null);
            }

            return $items;
        }
    }
}
