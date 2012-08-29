
ALTER TABLE `#__youtubegallery` ADD COLUMN `customlayout` text NOT NULL;
ALTER TABLE `#__youtubegallery` ADD COLUMN `randomization` tinyint(1) NOT NULL default 0;
ALTER TABLE `#__youtubegallery` ADD COLUMN `prepareheadtags` tinyint(1) NOT NULL default 0;