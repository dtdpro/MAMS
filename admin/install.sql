CREATE TABLE IF NOT EXISTS `#__mams_artauth` (
  `aa_id` int(11) NOT NULL AUTO_INCREMENT,
  `aa_art` int(11) NOT NULL,
  `aa_auth` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`aa_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_artcat` (
  `ac_id` int(11) NOT NULL AUTO_INCREMENT,
  `ac_art` int(11) NOT NULL,
  `ac_cat` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`ac_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_artdl` (
  `ad_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_dload` int(11) NOT NULL,
  `ad_art` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`ad_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_articles` (
  `art_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `art_sec` int(11) NOT NULL,
  `art_title` varchar(255) NOT NULL,
  `art_alias` varchar(255) NOT NULL,
  `art_thumb` varchar(255) NOT NULL,
  `art_desc` text NOT NULL,
  `art_keywords` text NOT NULL,
  `art_content` text NOT NULL,
  `art_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `art_published` datetime NOT NULL,
  `art_modified` datetime NOT NULL,
  `art_hits` int(11) NOT NULL,
  `art_show_related` tinyint(1) NOT NULL DEFAULT '1',
  `access` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`art_id`),
  KEY `art_title` (`art_title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_artmed` (
  `am_id` int(11) NOT NULL AUTO_INCREMENT,
  `am_art` int(11) NOT NULL,
  `am_media` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`am_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_authors` (
  `auth_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `auth_name` varchar(255) NOT NULL,
  `auth_alias` varchar(255) NOT NULL,
  `auth_credentials` text NOT NULL,
  `auth_bio` text NOT NULL,
  `auth_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `auth_modified` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`auth_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_cats` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_title` varchar(255) NOT NULL,
  `cat_alias` varchar(255) NOT NULL,
  `cat_desc` text NOT NULL,
  `cat_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cat_modified` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_dloads` (
  `dl_id` int(11) NOT NULL AUTO_INCREMENT,
  `dl_lname` varchar(50) NOT NULL,
  `dl_fname` varchar(255) NOT NULL,
  `dl_type` enum('pdf','mp3') NOT NULL,
  `dl_loc` varchar(255) NOT NULL,
  `dl_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dl_modified` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`dl_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_media` (
  `med_id` int(11) NOT NULL AUTO_INCREMENT,
  `med_type` enum('vid','vids','aud') NOT NULL,
  `med_title` varchar(255) NOT NULL,
  `med_file` varchar(255) NOT NULL,
  `med_still` varchar(255) NOT NULL,
  `med_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `med_modified` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`med_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_secs` (
  `sec_id` int(11) NOT NULL AUTO_INCREMENT,
  `sec_name` varchar(255) NOT NULL,
  `sec_alias` varchar(255) NOT NULL,
  `sec_desc` text NOT NULL,
  `sec_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sec_modified` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`sec_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_track` (
  `mt_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mt_user` int(11) NOT NULL,
  `mt_item` int(11) NOT NULL,
  `mt_type` enum('author','article','seclist','catlist','autlist','authors','dload') NOT NULL,
  `mt_session` varchar(60) NOT NULL,
  `mt_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mt_ipaddr` varchar(15) NOT NULL,
  PRIMARY KEY (`mt_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
