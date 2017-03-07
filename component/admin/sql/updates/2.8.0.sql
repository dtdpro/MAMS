ALTER TABLE `#__mams_cats` ADD `parent_id` INT NOT NULL DEFAULT '0' AFTER `cat_id`;
ALTER TABLE `#__mams_cats` ADD `level` INT NOT NULL , ADD `lft` INT NOT NULL , ADD `rgt` INT NOT NULL , ADD `path` VARCHAR(255) NOT NULL ;
ALTER TABLE `#__mams_cats` DROP `ordering`;