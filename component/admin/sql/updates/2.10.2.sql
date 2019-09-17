ALTER TABLE `#__mams_article_fields` ADD `field_show_author` BOOLEAN NOT NULL DEFAULT TRUE AFTER `field_show_module`;
ALTER TABLE `#__mams_tags` ADD `tag_icon` VARCHAR(255) NOT NULL AFTER `tag_image`;