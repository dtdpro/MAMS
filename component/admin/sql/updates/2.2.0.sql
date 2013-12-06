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

ALTER TABLE  `#__mams_secs` CHANGE  `sec_type`  `sec_type` ENUM(  'author',  'article',  'image' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'article';

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

INSERT INTO `#__mams_article_fields` (`field_id`, `field_name`, `field_title`, `field_rssname`, `field_type`, `field_group`, `field_show_page`, `field_show_list`, `field_show_module`, `ordering`, `access`, `published`, `params`) VALUES
(10, 'art-images', 'Article Images', '', 'images', 1, 1, 0, 0, 1000, 1, 1, '{"show_title_page":"0","show_title_desc":"0","show_title_module":"0","pretext":"","posttext":"","linktext":"0"}');

ALTER TABLE  `#__mams_cats` ADD  `cat_image` VARCHAR( 255 ) NOT NULL AFTER  `cat_desc` ;
ALTER TABLE  `#__mams_secs` ADD  `sec_image` VARCHAR( 255 ) NOT NULL AFTER  `sec_desc` ;
