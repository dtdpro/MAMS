<?php

class MAMSAuthorType
{
    public static function config()
    {
        $config = [

            'fields' => [

                'auth_name' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Full Name'
                    ],
                ],
                'auth_fname' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'First Name'
                    ],
                ],
                'auth_mi' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Middle Initial'
                    ],
                ],
                'auth_lname' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Last Name'
                    ],
                ],
                'auth_titles' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Titles'
                    ],
                ],
                'auth_credentials' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Credentials'
                    ]
                ],
                'auth_image' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Image'
                    ]
                ],
                'auth_id' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Id'
                    ]
                ],
                'url' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Author Link'
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
                'label' => 'MAMS Author'
            ]

        ];

        return $config;
    }

    public static function resolveUrl($obj, $args, $context, $info)
    {
        $dllink = "index.php?option=com_mams&view=author&secid=" . $obj->auth_sec . "&autid=" . $obj->auth_id . ":" . $obj->auth_alias ;
	    return JRoute::_($dllink);
    }

    public static function resolveDebug($obj, $args, $context, $info)
    {
        return print_r($obj,true);
    }
}
