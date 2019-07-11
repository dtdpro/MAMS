CREATE TABLE IF NOT EXISTS `#__mams_tags` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_title` varchar(255) NOT NULL,
  `tag_alias` varchar(255) NOT NULL,
  `tag_desc` text NOT NULL,
  `tag_image` varchar(255) NOT NULL,
  `tag_icon` varchar(255) NOT NULL,
  `tag_featured` tinyint(1) NOT NULL DEFAULT '0',
  `tag_feataccess` text NOT NULL,
  `tag_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tag_modified` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_arttag` (
  `at_id` int(11) NOT NULL AUTO_INCREMENT,
  `at_art` int(11) NOT NULL,
  `at_tag` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`at_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `#__mams_article_fields` (`field_id`, `field_name`, `field_title`, `field_rssname`, `field_type`, `field_group`, `field_show_page`, `field_show_list`, `field_show_module`, `ordering`, `access`, `published`, `params`) VALUES
(11, 'art-tags', 'Tags', '', 'tags', 1, 1, 0, 0, 0, 1, 1, '{"show_title_page":"0","show_title_desc":"0","show_title_module":"0","pretext":"","posttext":"","linktext":"0"}');