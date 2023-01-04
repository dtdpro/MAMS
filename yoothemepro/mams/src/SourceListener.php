<?php

use YOOtheme\Builder\Source;

class SourceListener
{
    /**
     * @param Source $source
     */
    public static function initSource($source)
    {
        $source->objectType('MAMSCatType', MAMSCatType::config());
        $source->queryType(MAMSCatQueryType::config());
    }
}
