ALTER TABLE `#__mams_articles` CHANGE `art_title` `art_title` VARCHAR(1024) NOT NULL;
ALTER TABLE `#__mams_articles` CHANGE `art_alias` `art_alias` VARCHAR(1024) NOT NULL;

ALTER TABLE `#__mams_track` CHANGE `mt_ipaddr` `mt_ipaddr` varchar(255) NOT NULL;


ALTER TABLE `#__mams_cats` ADD `cat_redirurl` VARCHAR(1024) NULL AFTER `cat_feataccess`;
ALTER TABLE `#__mams_secs` ADD `sec_redirurl` VARCHAR(1024) NULL AFTER `sec_thumb`;
ALTER TABLE `#__mams_tags` ADD `tag_redirurl` VARCHAR(1024) NULL AFTER `tag_feataccess`;

