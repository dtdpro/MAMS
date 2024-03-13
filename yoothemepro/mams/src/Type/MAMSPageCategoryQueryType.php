<?php


class MAMSPageCategoryQueryType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'mamscategory' => [
                    'type' => 'MAMSCategoryType',
                    'metadata' => [
                        'label' =>'MAMS Category',
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
        if (isset($root['category'])) {
            return $root['category'];
        }
    }
}
