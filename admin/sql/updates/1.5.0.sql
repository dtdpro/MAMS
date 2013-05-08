ALTER TABLE  `#__mams_media` CHANGE  `med_title`  `med_inttitle` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE  `#__mams_media` ADD  `med_exttitle` VARCHAR( 255 ) NOT NULL AFTER  `med_inttitle` ,
ADD  `med_desc` TEXT NOT NULL AFTER  `med_exttitle`;

