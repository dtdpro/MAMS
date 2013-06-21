ALTER TABLE `#__mams_artauth` ENGINE = InnoDB;
ALTER TABLE `#__mams_artcat` ENGINE = InnoDB;
ALTER TABLE `#__mams_artdl` ENGINE = InnoDB;
ALTER TABLE `#__mams_artfeat` ENGINE = InnoDB;
ALTER TABLE `#__mams_articles` ENGINE = InnoDB;
ALTER TABLE `#__mams_artmed` ENGINE = InnoDB;
ALTER TABLE `#__mams_authors` ENGINE = InnoDB;
ALTER TABLE `#__mams_cats` ENGINE = InnoDB;
ALTER TABLE `#__mams_dloads` ENGINE = InnoDB;
ALTER TABLE `#__mams_media` ENGINE = InnoDB;
ALTER TABLE `#__mams_mediafeat` ENGINE = InnoDB;
ALTER TABLE `#__mams_mediatrack` ENGINE = InnoDB;
ALTER TABLE `#__mams_secs` ENGINE = InnoDB;
ALTER TABLE `#__mams_track` ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__mams_artlinks` (
  `al_id` int(11) NOT NULL AUTO_INCREMENT,
  `al_art` int(11) NOT NULL,
  `al_link` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`al_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__mams_links` (
  `link_id` int(11) NOT NULL AUTO_INCREMENT,
  `link_url` varchar(1024) NOT NULL,
  `link_title` varchar(255) NOT NULL,
  `link_target` VARCHAR( 10 ) NOT NULL DEFAULT '_blank',
  `link_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `link_modified` DATETIME NOT NULL,
  `published` tinyint(4) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
