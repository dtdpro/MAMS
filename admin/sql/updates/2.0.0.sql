ALTER TABLE `#__mams_authors` CHANGE `auth_name` `auth_lname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `#__mams_authors` ADD `auth_fname` VARCHAR( 255 ) NOT NULL AFTER `auth_sec` ,
ADD `auth_mi` VARCHAR( 5 ) NOT NULL AFTER `auth_fname`;
ALTER TABLE `#__mams_authors` ADD `auth_titles` VARCHAR( 255 ) NOT NULL AFTER `auth_lname` ;