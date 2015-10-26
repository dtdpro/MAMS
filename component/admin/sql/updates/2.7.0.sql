ALTER TABLE `#__mams_secs` ADD `parent_id` INT NOT NULL DEFAULT '0' AFTER `sec_id`;
ALTER TABLE `#__mams_secs` ADD `level` INT NOT NULL , ADD `lft` INT NOT NULL , ADD `rgt` INT NOT NULL , ADD `path` VARCHAR(255) NOT NULL ;
ALTER TABLE `#__mams_secs` CHANGE `sec_desc` `sec_content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `#__mams_secs` ADD `sec_desc` TEXT NOT NULL AFTER `sec_alias`;
ALTER TABLE `#__mams_secs` DROP `ordering`;
ALTER TABLE `#__mams_secs` ADD `sec_thumb` VARCHAR(255) NOT NULL AFTER `sec_image`;
ALTER TABLE `#__mams_articles` ADD `art_excluded` BOOLEAN NOT NULL DEFAULT FALSE AFTER `art_added_by`;