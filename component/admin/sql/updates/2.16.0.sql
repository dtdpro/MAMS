ALTER TABLE `#__mams_articles` ADD `art_preview` longtext NOT NULL AFTER `art_content`;
ALTER TABLE `#__mams_article_fields` ADD `field_show_preview` BOOLEAN NOT NULL DEFAULT TRUE AFTER `field_show_page`;