ALTER TABLE `#__mams_articles` CHANGE `art_fielddata` `art_fielddata` TEXT NULL, CHANGE `metadata` `metadata` TEXT NULL, CHANGE `params` `params` TEXT NULL;
ALTER TABLE `#__mams_artfeat` CHANGE `ordering` `ordering` INT(11) NOT NULL DEFAULT '0';