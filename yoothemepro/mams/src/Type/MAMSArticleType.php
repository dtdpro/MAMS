<?php

class MAMSArticleType
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
                'url' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => 'Article Link',
	                    'arguments' => [
		                    'article_seclock' => [
			                    'text' => 'Section Lock',
			                    'type' => 'checkbox',
			                    'default' => true
		                    ],
		                    'article_catlock' => [
			                    'text' => 'Category Lock',
			                    'type' => 'checkbox',
			                    'default' => true
		                    ],
		                    'article_taglock' => [
			                    'text' => 'Tag Lock',
			                    'type' => 'checkbox',
			                    'default' => true
		                    ]
	                    ]
                    ],
                    'args' => [
		                'article_seclock' => [
			                'type' => 'Boolean',
		                ],
		                'article_catlock' => [
			                'type' => 'Boolean',
		                ],
		                'article_taglock' => [
			                'type' => 'Boolean',
		                ]
	                ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolveUrl'
                    ]
                ],
                'thumnb' => [
	                'type' => 'String',
	                'metadata' => [
		                'label' => 'Article Thumbnail'
	                ],
	                'extensions' => [
		                'call' => __CLASS__ . '::resolveThumbnail'
	                ]
                ],
                'desc' => [
	                'type' => 'String',
	                'metadata' => [
		                'label' => 'Article Description'
	                ],
	                'extensions' => [
		                'call' => __CLASS__ . '::resolveDesc'
	                ]
                ],
                'section' => [
	                'type' => 'String',
	                'metadata' => [
		                'label' => 'Article Section',
		                'arguments' => [
			                'link_section' => [
				                'text' => 'Link Section',
				                'type' => 'checkbox',
				                'default' => false
			                ]
		                ]
	                ],
	                'extensions' => [
		                'call' => __CLASS__ . '::resolveSection'
	                ],
	                'args' => [
		                'link_section' => [
			                'type' => 'Boolean',
		                ]
	                ],
                ],
                'cats' => [
	                'type' => 'String',
	                'metadata' => [
		                'label' => 'Article Categories',
		                'arguments' => [
			                'link_category' => [
				                'text' => 'Link Category',
				                'type' => 'checkbox',
				                'default' => false
			                ]
		                ]
	                ],
	                'extensions' => [
		                'call' => __CLASS__ . '::resolveCats'
	                ],
	                'args' => [
		                'link_category' => [
			                'type' => 'Boolean',
		                ]
	                ],
                ],
                'tags' => [
	                'type' => 'String',
	                'metadata' => [
		                'label' => 'Article Tags',
		                'arguments' => [
			                'link_tag' => [
				                'text' => 'Link Tags',
				                'type' => 'checkbox',
				                'default' => false
			                ]
		                ]
	                ],
	                'extensions' => [
		                'call' => __CLASS__ . '::resolveTags'
	                ],
	                'args' => [
		                'link_tag' => [
			                'type' => 'Boolean',
		                ]
	                ],
                ],
                'authors' => [
	                'type' => 'String',
	                'metadata' => [
		                'label' => 'Article Authors',
		                'arguments' => [
			                'link_author' => [
				                'text' => 'Link Authors',
				                'type' => 'checkbox',
				                'default' => false
			                ]
		                ]
	                ],
	                'extensions' => [
		                'call' => __CLASS__ . '::resolveAuthors'
	                ],
	                'args' => [
		                'link_author' => [
			                'type' => 'Boolean',
		                ]
	                ],
                ]

            ],

            'metadata' => [
                'type' => true,
                'label' => 'MAMS Article'
            ]

        ];
    }

    public static function resolveTitle($obj, $args, $context, $info)
    {
        return $obj->art_title;
    }

    public static function resolveId($obj, $args, $context, $info)
    {
        return $obj->art_id;
    }

    public static function resolveUrl($obj, $args, $context, $info)
    {
	    $artlink = "index.php?option=com_mams&view=article";
	    if ($args['article_seclock']) $artlink .= "&secid=" . $obj->art_sec . ":" . $obj->sec_alias;
	    $artlink .= "&artid=" . $obj->art_id . ":" . $obj->art_alias;
	    if ($obj->cats && $args['article_catlock']) $artlink .= '&catid=' . $obj->cats[0]->cat_id;
	    if ($obj->tags && $args['article_taglock']) $artlink .= '&tagid=' . $obj->tags[0]->tag_id;

	    return JRoute::_($artlink);
    }

	public static function resolveThumbnail($obj, $args, $context, $info)
	{
		return $obj->art_thumb;
	}

	public static function resolveDesc($obj, $args, $context, $info)
	{
		return $obj->art_desc;
	}

	public static function resolveSection($obj, $args, $context, $info)
	{
		$secText = '';
		if ($args['link_section']) $secText .= '<a href="'.JRoute::_("index.php?option=com_mams&view=artlist&layout=section&secid=".$obj->sec_id.":".$obj->sec_alias).'">';
		$secText .= $obj->sec_name;
		if ($args['link_section']) $secText .= '</a>';
		return $secText;
	}

	public static function resolveCats($obj, $args, $context, $info)
	{
		$catsOutput = [];
		foreach ($obj->cats as $cat) {
			$catText = '';
			if ($args['link_category']) $catText = '<a href="'.JRoute::_("index.php?option=com_mams&view=category&catid=".$cat->cat_id.":".$cat->cat_alias).'">';
			$catText .= $cat->cat_title;
			if ($args['link_category']) $catText .= '</a>';
			$catsOutput[] = $catText;
		}
		return implode(",",$catsOutput);
	}

	public static function resolveTags($obj, $args, $context, $info)
	{
		$tagsOutput = [];
		foreach ($obj->tags as $tag) {
			$tagText = '';
			if ($args['link_tag']) $tagText = '<a href="'.JRoute::_("index.php?option=com_mams&view=tag&tagid=".$tag->tag_id.":".$tag->tag_alias).'">';
			$tagText .= $tag->tag_title;
			if ($args['link_tag']) $tagText .= '</a>';
			$tagsOutput[] = $tagText;
		}
		return implode(",",$tagsOutput);
	}

	public static function resolveAuthors($obj, $args, $context, $info)
	{
		$authorsOutput = [];
		foreach ($obj->auts as $aut) {
			$auth = '';
			if ($args['link_author']) $auth .= '<a href="'.JRoute::_("index.php?option=com_mams&view=author&secid=".$aut->auth_sec."&autid=".$aut->auth_id.":".$aut->auth_alias).'">';
			$auth .= $aut->auth_name;
			if ($args['link_author']) $auth .= '</a>';
			$authorsOutput[] = $auth;
		}
		return implode(",",$authorsOutput);
	}
    
}
