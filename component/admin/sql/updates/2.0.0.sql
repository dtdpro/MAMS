ALTER TABLE `#__mams_authors` CHANGE `auth_name` `auth_lname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `#__mams_authors` ADD `auth_fname` VARCHAR( 255 ) NOT NULL AFTER `auth_sec` ,
ADD `auth_mi` VARCHAR( 5 ) NOT NULL AFTER `auth_fname`;
ALTER TABLE `#__mams_authors` ADD `auth_titles` VARCHAR( 255 ) NOT NULL AFTER `auth_lname` ;
ALTER TABLE  `#__mams_articles` ADD  `art_fielddata` TEXT NOT NULL AFTER  `art_show_related`;

ALTER TABLE  `#__mams_artauth` ADD  `aa_field` INT NOT NULL DEFAULT  '5' AFTER  `aa_id` , ADD INDEX (  `aa_field` );
ALTER TABLE  `#__mams_artmed` ADD  `am_field` INT NOT NULL DEFAULT  '6' AFTER  `am_id` , ADD INDEX (  `am_field` );
ALTER TABLE  `#__mams_artdl` ADD  `ad_field` INT NOT NULL DEFAULT  '7' AFTER  `ad_id` , ADD INDEX (  `ad_field` );
ALTER TABLE  `#__mams_artlinks` ADD  `al_field` INT NOT NULL DEFAULT  '8' AFTER  `al_id` , ADD INDEX (  `al_field` );