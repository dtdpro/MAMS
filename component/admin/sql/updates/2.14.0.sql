ALTER TABLE `#__mams_secs` CHANGE `asset_id` `asset_id` INT NOT NULL DEFAULT '0';
ALTER TABLE `#__mams_authors` CHANGE `auth_mirror` `auth_mirror` INT NOT NULL DEFAULT '0';
ALTER TABLE `#__mams_authors` DROP COLUMN `metadata`;
ALTER TABLE `#__mams_mediafeat` CHANGE `ordering` `ordering` INT NOT NULL DEFAULT '0';

ALTER TABLE `#__mams_articles` CHANGE `checked_out` `checked_out` INT NULL, CHANGE `checked_out_time` `checked_out_time` DATETIME NULL;
ALTER TABLE `#__mams_articles` CHANGE `art_hits` `art_hits` INT NOT NULL DEFAULT '0';
ALTER TABLE `#__mams_articles` CHANGE `asset_id` `asset_id` INT NOT NULL DEFAULT '0';