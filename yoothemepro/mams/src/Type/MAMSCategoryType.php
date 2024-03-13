<?php

class MAMSCategoryType
{
    public static function config()
    {
        return [

            'fields' => [

                'title' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Title'
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveTitle'
                    ]
                ],
                'id' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Id'
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveId'
                    ]
                ],
                'content' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Id'
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveContent'
                    ]
                ],
                'url' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Article List Link'
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveUrl'
                    ]
                ]

            ],

            'metadata' => [
                'type' => true,
                'label' => 'MAMS Category'
            ]

        ];
    }

    public static function resolveTitle($obj, $args, $context, $info)
    {
        return $obj->cat_title;
    }

    public static function resolveId($obj, $args, $context, $info)
    {
        return $obj->cat_id;
    }

    public static function resolveContent($obj, $args, $context, $info)
    {
        return $obj->cat_content;
    }

    public static function resolveUrl($obj, $args, $context, $info)
    {
        return JRoute::_("index.php?option=com_mams&view=artlist&layout=section&catid=".$obj->cat_id.':'.$obj->cat_alias);
    }
    
}
