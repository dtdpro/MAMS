<?php

class MAMSDownloadType
{
    public static function config()
    {
        $config = [

            'fields' => [

                'dl_lname' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Link Name'
                    ],
                ],
                'dl_fname' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'File Name'
                    ],
                ],
                'dl_id' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Id'
                    ]
                ],
                'dl_loc' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Location'
                    ]
                ],
                'dl_type' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Type'
                    ]
                ],
                'url' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Download Link'
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveUrl'
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
                'label' => 'MAMS Download'
            ]

        ];

        return $config;
    }

    public static function resolveUrl($obj, $args, $context, $info)
    {
        $dllink = "components/com_mams/dl.php?dlid=".$obj->dl_id;
	    return JRoute::_($dllink);
    }

    public static function resolveDebug($obj, $args, $context, $info)
    {
        return print_r($obj,true);
    }
}
