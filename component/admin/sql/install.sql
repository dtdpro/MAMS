CREATE TABLE IF NOT EXISTS `#__mams_artauth` (
  `aa_id` int(11) NOT NULL AUTO_INCREMENT,
  `aa_field` int(11) NOT NULL DEFAULT '5',
  `aa_art` int(11) NOT NULL,
  `aa_auth` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`aa_id`),
  KEY `aa_field` (`aa_field`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_artcat` (
  `ac_id` int(11) NOT NULL AUTO_INCREMENT,
  `ac_art` int(11) NOT NULL,
  `ac_cat` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`ac_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_artdl` (
  `ad_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_field` int(11) NOT NULL DEFAULT '7',
  `ad_dload` int(11) NOT NULL,
  `ad_art` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`ad_id`),
  KEY `ad_field` (`ad_field`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_artfeat` (
  `af_id` int(11) NOT NULL AUTO_INCREMENT,
  `af_art` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`af_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_articles` (
  `art_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `art_sec` int(11) NOT NULL,
  `art_title` varchar(255) NOT NULL,
  `art_alias` varchar(255) NOT NULL,
  `art_thumb` varchar(255) NOT NULL,
  `art_desc` text NOT NULL,
  `metadesc` text NOT NULL,
  `metakey` text NOT NULL,
  `art_content` text NOT NULL,
  `art_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `art_publish_up` date NOT NULL,
  `art_publish_down` date NOT NULL,
  `art_modified` datetime NOT NULL,
  `art_modified_by` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `art_added_by` int(11) NOT NULL,
  `art_hits` int(11) NOT NULL,
  `art_fielddata` text NOT NULL,
  `access` int(11) NOT NULL,
  `feataccess` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `metadata` text NOT NULL,
  `params` text NOT NULL,
  `version` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  PRIMARY KEY (`art_id`),
  KEY `art_title` (`art_title`),
  KEY `asset_id` (`asset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_artimg` (
  `ai_id` int(11) NOT NULL AUTO_INCREMENT,
  `ai_field` int(11) NOT NULL DEFAULT '10',
  `ai_art` int(11) NOT NULL,
  `ai_image` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`ai_id`),
  KEY `am_field` (`ai_field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__mams_article_fieldgroups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) NOT NULL,
  `group_title` varchar(255) NOT NULL,
  `group_show_title` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100;

CREATE TABLE IF NOT EXISTS `#__mams_article_fields` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_name` varchar(100) NOT NULL,
  `field_title` varchar(255) NOT NULL,
  `field_rssname` varchar(100) NOT NULL,
  `field_type` varchar(20) NOT NULL,
  `field_group` int(11) NOT NULL,
  `field_show_page` tinyint(1) NOT NULL DEFAULT '0',
  `field_show_list` tinyint(1) NOT NULL DEFAULT '0',
  `field_show_module` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100;

CREATE TABLE IF NOT EXISTS `#__mams_artlinks` (
  `al_id` int(11) NOT NULL AUTO_INCREMENT,
  `al_field` int(11) NOT NULL DEFAULT '8',
  `al_art` int(11) NOT NULL,
  `al_link` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`al_id`),
  KEY `al_field` (`al_field`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_artmed` (
  `am_id` int(11) NOT NULL AUTO_INCREMENT,
  `am_field` int(11) NOT NULL DEFAULT '6',
  `am_art` int(11) NOT NULL,
  `am_media` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`am_id`),
  KEY `am_field` (`am_field`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_authors` (
  `auth_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `auth_sec` int(11) NOT NULL,
  `auth_fname` varchar(255) NOT NULL,
  `auth_mi` varchar(5) NOT NULL,
  `auth_lname` varchar(255) NOT NULL,
  `auth_titles` varchar(255) NOT NULL,
  `auth_name` varchar(255) NOT NULL,
  `auth_alias` varchar(255) NOT NULL,
  `auth_credentials` text NOT NULL,
  `auth_bio` text NOT NULL,
  `auth_image` varchar(255) NOT NULL,
  `auth_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `auth_modified` datetime NOT NULL,
  `auth_mirror` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `metadata` text NOT NULL,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_cats` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_title` varchar(255) NOT NULL,
  `cat_alias` varchar(255) NOT NULL,
  `cat_desc` text NOT NULL,
  `cat_image` VARCHAR( 255 ) NOT NULL,
  `cat_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cat_modified` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_dloads` (
  `dl_id` int(11) NOT NULL AUTO_INCREMENT,
  `dl_extension` varchar(255) NOT NULL DEFAULT 'com_mams',
  `dl_lname` varchar(50) NOT NULL,
  `dl_fname` varchar(255) NOT NULL,
  `dl_type` enum('pdf','mp3') NOT NULL,
  `dl_loc` varchar(255) NOT NULL,
  `dl_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dl_modified` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`dl_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_images` (
  `img_id` int(11) NOT NULL AUTO_INCREMENT,
  `img_sec` int(11) NOT NULL,
  `img_inttitle` varchar(255) NOT NULL,
  `img_exttitle` varchar(255) NOT NULL,
  `img_desc` text NOT NULL,
  `img_thumb` varchar(255) NOT NULL,
  `img_full` varchar(255) NOT NULL,
  `img_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `img_modified` datetime NOT NULL,
  `img_extension` varchar(100) NOT NULL DEFAULT 'com_mams',
  `published` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__mams_links` (
  `link_id` int(11) NOT NULL AUTO_INCREMENT,
  `link_url` varchar(1024) NOT NULL,
  `link_title` varchar(255) NOT NULL,
  `link_target` varchar(10) NOT NULL DEFAULT '_blank',
  `link_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `link_modified` datetime NOT NULL,
  `published` tinyint(4) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_media` (
  `med_id` int(11) NOT NULL AUTO_INCREMENT,
  `med_extension` varchar(255) NOT NULL DEFAULT 'com_mams',
  `med_type` enum('vid','vids','aud') NOT NULL,
  `med_inttitle` varchar(255) NOT NULL,
  `med_exttitle` varchar(255) NOT NULL,
  `med_desc` text NOT NULL,
  `med_postroll` text NOT NULL,
  `med_file` varchar(255) NOT NULL,
  `med_still` varchar(255) NOT NULL,
  `med_autoplay` tinyint(1) NOT NULL DEFAULT '0',
  `med_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `med_modified` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `feataccess` int(11) NOT NULL,
  PRIMARY KEY (`med_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_mediafeat` (
  `mf_id` int(11) NOT NULL AUTO_INCREMENT,
  `mf_media` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`mf_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_secs` (
  `sec_id` int(11) NOT NULL AUTO_INCREMENT,
  `sec_type` enum('author','article','image') NOT NULL DEFAULT 'article',
  `sec_name` varchar(255) NOT NULL,
  `sec_alias` varchar(255) NOT NULL,
  `sec_desc` text NOT NULL,
  `sec_image` VARCHAR( 255 ) NOT NULL,
  `sec_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sec_modified` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `metadata` text NOT NULL,
  `asset_id` int(11) NOT NULL,
  PRIMARY KEY (`sec_id`),
  KEY `asset_id` (`asset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_track` (
  `mt_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mt_user` int(11) NOT NULL,
  `mt_item` int(11) NOT NULL,
  `mt_type` varchar(50) NOT NULL,
  `mt_session` varchar(60) NOT NULL,
  `mt_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mt_ipaddr` varchar(15) NOT NULL,
  PRIMARY KEY (`mt_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) VALUES
('MAMS Article', 'com_mams.article', '{"special":{"dbtable":"#__mams_articles","key":"art_id","type":"Article","prefix":"MAMSTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":{"core_content_item_id":"art_id","core_title":"art_title","core_state":"state","core_alias":"art_alias","core_created_time":"art_added","core_modified_time":"art_modified","core_body":"art_content", "core_hits":"art_hits","core_publish_up":"art_publish_up","core_publish_down":"art_publish_down","core_access":"access", "core_params":"params", "core_metadata":"metadata", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "asset_id":"asset_id","core_catid":"art_sec"}}', 'MAMSHelperRoute::getArticleRoute', '{"formFile":"administrator\\/components\\/com_mams\\/models\\/forms\\/article.xml", "hideFields":["asset_id","checked_out","checked_out_time","version"],"ignoreChanges":["art_modified_by", "art_modified", "checked_out", "checked_out_time", "version", "art_hits"],"convertToInt":["art_publish_up", "art_publish_down", "ordering"],"displayLookup":[{"sourceColumn":"art_sec","targetTable":"#__mams_secs","targetColumn":"sec_id","displayColumn":"sec_name"},{"sourceColumn":"art_added_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"art_modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"} ]}'),
('MAMS Author', 'com_mams.auth', '{"special":{"dbtable":"#__mams_authors","key":"auth_id","type":"Auth","prefix":"MAMSTable","config":"array()"},"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":[{"core_content_item_id":"auth_id","core_title":"auth_name","core_state":"published","core_alias":"auth_alias","core_created_time":"auth_added","core_modified_time":"auth_modified","core_body":"auth_bio", "core_access":"access", "core_params":"params", "core_metadata":"metadata", "core_ordering":"ordering", "asset_id":"asset_id","core_catid":"auth_sec"}]}', 'MAMSHelperRoute::getAuthorRoute', NULL),
('MAMS Section', 'com_mams.sec', '{"special":{"dbtable":"#__mams_secs","key":"sec_id","type":"Sec","prefix":"MAMSTable","config":"array()"},"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":[{"core_content_item_id":"sec_id","core_title":"sec_name","core_state":"published","core_alias":"sec_alias","core_created_time":"sec_added","core_modified_time":"auth_modified","core_body":"sec_desc", "core_access":"access", "core_params":"params", "core_metadata":"metadata", "core_ordering":"ordering", "asset_id":"asset_id"}]}', 'MAMSHelperRoute::getSectionRoute', NULL);

INSERT INTO `#__mams_article_fieldgroups` (`group_id`, `group_name`, `group_title`, `group_show_title`, `ordering`, `access`, `published`) VALUES
(1, 'article', 'Main', 0, 1, 1, 1);

INSERT INTO `#__mams_article_fields` (`field_id`, `field_name`, `field_title`, `field_rssname`, `field_type`, `field_group`, `field_show_page`, `field_show_list`, `field_show_module`, `ordering`, `access`, `published`, `params`) VALUES
(1, 'art-title', 'Article Title', 'title', 'title', 1, 1, 1, 0, 1, 1, 1, '{"show_title_page":"0","show_title_desc":"0","pretext":"","posttext":"","linktext":"0"}'),
(2, 'art-desc', 'Article Description', 'description', 'desc', 1, 0, 1, 0, 4, 1, 1, '{"show_title_page":"0","show_title_desc":"0","pretext":"","posttext":"","linktext":"0"}'),
(3, 'art-content', 'Article Body', 'body', 'body', 1, 1, 0, 0, 9, 1, 1, '{"show_title_page":"0","show_title_desc":"0","pretext":"","posttext":"","linktext":"0"}'),
(4, 'art-pubinfo', 'Article Publishing Information', '', 'pubinfo', 1, 1, 1, 0, 3, 1, 1, '{"show_title_page":"0","show_title_desc":"0","pretext":"","posttext":"","linktext":"0"}'),
(5, 'art-auths', 'Article Authors', 'author', 'auths', 1, 1, 1, 0, 8, 1, 1, '{"show_title_page":"0","show_title_desc":"0","pretext":"","posttext":"","linktext":"0"}'),
(6, 'art-media', 'Article Media', '', 'media', 1, 1, 0, 0, 5, 1, 1, '{"show_title_page":"0","show_title_desc":"0","pretext":"","posttext":"","linktext":"0"}'),
(7, 'art-dloads', 'Article Downloads', '', 'dloads', 1, 1, 0, 0, 7, 1, 1, '{"show_title_page":"0","show_title_desc":"0","pretext":"","posttext":"","linktext":"0"}'),
(8, 'art-links', 'Article Links', '', 'links', 1, 1, 0, 0, 10, 1, 1, '{"show_title_page":"0","show_title_desc":"0","pretext":"","posttext":"","linktext":"0"}'),
(9, 'art-related', 'Related Items', '', 'related', 1, 1, 0, 0, 11, 1, 1, '{"show_title_page":"1","show_title_desc":"0","pretext":"","posttext":"","linktext":"0"}'),
(10, 'art-images', 'Article Images', '', 'images', 1, 1, 0, 0, 6, 1, 1, '{"show_title_page":"0","show_title_desc":"0","show_title_module":"0","pretext":"","posttext":"","linktext":"0"}');


