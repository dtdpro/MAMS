CREATE TABLE IF NOT EXISTS `#__mams_mediafeat` (
  `mf_id` int(11) NOT NULL AUTO_INCREMENT,
  `mf_media` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`mf_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE  `#__mams_media` ADD  `feataccess` INT NOT NULL DEFAULT  '1';