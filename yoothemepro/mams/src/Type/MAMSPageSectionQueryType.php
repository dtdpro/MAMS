<?php


class MAMSPageSectionQueryType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'mamssection' => [
                    'type' => 'MAMSSectionType',
                    'metadata' => [
                        'label' =>'MAMS Section',
                        'view' => ['com_mams.artlist'],
                        'group' => 'MAMS',
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
            ],
        ];
    }

    public static function resolve($root)
    {
        if (isset($root['section'])) {
            return $root['section'];
        }
    }
}
