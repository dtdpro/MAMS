<?php

class MAMSSectionType
{
    public static function config()
    {
        return [
            'fields' => [
                'title' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Name'
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
                        'label' => 'Content'
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
                'label' => 'MAMS Section'
            ]
        ];
    }

    public static function resolveTitle($obj, $args, $context, $info)
    {
        return $obj->sec_name;
    }

    public static function resolveId($obj, $args, $context, $info)
    {
        return $obj->sec_id;
    }

    public static function resolveContent($obj, $args, $context, $info)
    {
        return $obj->sec_content;
    }

    public static function resolveUrl($obj, $args, $context, $info)
    {
        return JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$obj->sec_id.':'.$obj->sec_alias);
    }
    
}
