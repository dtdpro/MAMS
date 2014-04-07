ALTER TABLE `#__mams_cats` ADD `cat_featured` BOOLEAN NOT NULL DEFAULT FALSE AFTER `cat_image`,
ADD `cat_feataccess` INT NOT NULL DEFAULT '1' AFTER `cat_featured`;