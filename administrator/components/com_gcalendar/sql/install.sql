DROP TABLE IF EXISTS `#__gcalendar`;

CREATE TABLE IF NOT EXISTS `#__gcalendar` (
  `id` int(11) NOT NULL auto_increment,
  `calendar_id` text NOT NULL,
  `name` text NOT NULL,
  `magic_cookie` text NOT NULL,
  `color` text NOT NULL,
  PRIMARY KEY  (`id`)
) ;
