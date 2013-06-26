ALTER TABLE `#__mams_authors` CHANGE `auth_name` `auth_lname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `#__mams_authors` ADD `auth_fname` VARCHAR( 255 ) NOT NULL AFTER `auth_sec` ,
ADD `auth_mi` VARCHAR( 5 ) NOT NULL AFTER `auth_fname`;
ALTER TABLE `#__mams_authors` ADD `auth_titles` VARCHAR( 255 ) NOT NULL AFTER `auth_lname` ;

ALTER TABLE  `#__mams_articles` ADD  `art_fielddata` TEXT NOT NULL AFTER  `art_show_related`;
ALTER TABLE  `#__mams_articles` ADD  `metadata` TEXT NOT NULL ;
ALTER TABLE  `#__mams_articles` ADD  `params` TEXT NOT NULL ;
ALTER TABLE  `#__mams_articles` CHANGE  `art_published`  `art_publish_up` DATE NOT NULL ;
ALTER TABLE  `#__mams_articles` ADD  `art_publish_down` DATE NOT NULL AFTER  `art_publish_up` ;
ALTER TABLE  `#__mams_articles` ADD  `metadesc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `art_desc`;
ALTER TABLE  `#__mams_articles` CHANGE  `art_keywords`  `metakey` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE  `#__mams_articles` DROP  `art_show_related` ;
ALTER TABLE  `#__mams_articles` ADD  `art_added_by` INT NOT NULL AFTER  `art_added` ;
ALTER TABLE  `#__mams_articles` ADD  `art_modified_by` INT NOT NULL AFTER  `art_modified` ;
ALTER TABLE  `#__mams_articles` ADD  `checked_out` INT NOT NULL AFTER  `art_modified_by` ,
ADD  `checked_out_time` DATETIME NOT NULL AFTER  `checked_out` ;

ALTER TABLE  `#__mams_artauth` ADD  `aa_field` INT NOT NULL DEFAULT  '5' AFTER  `aa_id` , ADD INDEX (  `aa_field` );
ALTER TABLE  `#__mams_artmed` ADD  `am_field` INT NOT NULL DEFAULT  '6' AFTER  `am_id` , ADD INDEX (  `am_field` );
ALTER TABLE  `#__mams_artdl` ADD  `ad_field` INT NOT NULL DEFAULT  '7' AFTER  `ad_id` , ADD INDEX (  `ad_field` );
ALTER TABLE  `#__mams_artlinks` ADD  `al_field` INT NOT NULL DEFAULT  '8' AFTER  `al_id` , ADD INDEX (  `al_field` );


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`) VALUES
('MAMS Article', 'com_mams.article', '{"special":{"dbtable":"#__mams_articles","key":"art_id","type":"MAMS","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__core_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}', '', '{"common":[{"core_content_item_id":"art_id","core_title":"art_title","core_state":"published","core_alias":"art_alias","core_created_time":"art_added","core_modified_time":"art_modified","core_body":"art_content", "core_hits":"art_hits","core_publish_up":"art_publish_up","core_publish_down":"art_publish_down","core_access":"access", "core_params":"params", "core_metadata":"metadata", "core_version":"art_version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "asset_id":"asset_id","core_catid":"art_sec"}]}', 'MAMSHelperRoute::getArticleRoute');

CREATE TABLE IF NOT EXISTS `joomla311_mams_article_fieldgroups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) NOT NULL,
  `group_title` varchar(255) NOT NULL,
  `group_show_title` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO `#__mams_article_fieldgroups` (`group_id`, `group_name`, `group_title`, `group_show_title`, `ordering`, `access`, `published`) VALUES
(1, 'article', 'Main', 0, 1, 1, 1);

CREATE TABLE IF NOT EXISTS `#__mams_article_fields` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_name` varchar(100) NOT NULL,
  `field_title` varchar(255) NOT NULL,
  `field_rssname` varchar(100) NOT NULL,
  `field_type` varchar(20) NOT NULL,
  `field_group` int(11) NOT NULL,
  `field_show_page` tinyint(1) NOT NULL DEFAULT '0',
  `field_show_title` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

INSERT INTO `#__mams_article_fields` (`field_id`, `field_name`, `field_title`, `field_rssname`, `field_type`, `field_group`, `field_show_page`, `field_show_title`, `ordering`, `access`, `published`) VALUES
(1, 'art_title', 'Article Title', 'title', 'textfield', 1, 1, 0, 4, 1, 1),
(2, 'art_desc', 'Article Description', 'description', 'textbox', 1, 0, 0, 6, 1, 1),
(3, 'art_content', 'Article Body', 'body', 'editor', 1, 1, 0, 7, 1, 1),
(4, 'art_pubinfo', 'Article Publishing Information', '', 'pubinfo', 1, 1, 0, 5, 1, 1),
(5, 'art_auths', 'Article Authors', 'author', 'auths', 1, 1, 0, 8, 1, 1),
(6, 'art_media', 'Article Media', '', 'media', 1, 1, 0, 9, 1, 1),
(7, 'art_dloads', 'Article Downloads', '', 'dloads', 1, 1, 0, 10, 1, 1),
(8, 'art_links', 'Article Links', '', 'links', 1, 1, 0, 11, 1, 1),
(9, 'art_related', 'Related Items', '', 'related', 1, 1, 1, 19, 1, 1);
