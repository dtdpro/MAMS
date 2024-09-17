<?php

class MAMSLinkType
{
    public static function config()
    {
        $config = [

            'fields' => [

                'link_title' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Link Title'
                    ],
                ],
                'link_url' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Link URL'
                    ],
                ],
                'track_link_url' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Trackable Link URL'
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveTrackLink'
                    ]
                ],
                'link_id' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Id'
                    ]
                ],
                'debug' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Debug Information'
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveDebug'
                    ]
                ]

            ],

            'metadata' => [
                'type' => true,
                'label' => 'MAMS Link'
            ]

        ];

        return $config;
    }

    public static function resolveDebug($obj, $args, $context, $info)
    {
        return print_r($obj,true);
        //JRoute::_( "components/com_mams/lk.php?linkid=" . $d->link_id )
    }

    public static function resolveTrackLink($obj, $args, $context, $info)
    {
        return JRoute::_( "components/com_mams/lk.php?linkid=" . $obj->link_id );
    }
}
