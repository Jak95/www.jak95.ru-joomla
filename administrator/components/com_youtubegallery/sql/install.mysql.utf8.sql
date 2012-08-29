CREATE TABLE IF NOT EXISTS `#__youtubegallery` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `galleryname` varchar(50) NOT NULL,
  `gallerylist` text,
  `showtitle` tinyint(1) NOT NULL,
  `playvideo` tinyint(1) NOT NULL,
  `repeat` tinyint(1) NOT NULL,
  `fullscreen` tinyint(1) NOT NULL,
  `autoplay` tinyint(1) NOT NULL,
  `related` tinyint(1) NOT NULL,
  `showinfo` tinyint(1) NOT NULL,
  `bgcolor` varchar(20) NOT NULL,
  `cols` smallint(6) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `cssstyle` varchar(255) NOT NULL,
  `navbarstyle` varchar(255) NOT NULL,
  `thumbnailstyle` varchar(255) NOT NULL,
  `linestyle` varchar(255) NOT NULL,
  `showgalleryname` varchar(255) NOT NULL,
  `gallerynamestyle` varchar(255) NOT NULL,
  `showactivevideotitle` varchar(255) NOT NULL,
  `activevideotitlestyle` varchar(255) NOT NULL,
  `color1` varchar(255) NOT NULL,
  `color2` varchar(255) NOT NULL,
  `border` tinyint(1) NOT NULL,
  `description` tinyint(1) NOT NULL,
  `descr_position` smallint(6) NOT NULL,
  `descr_style` varchar(255) NOT NULL,
  `openinnewwindow` tinyint(1) NOT NULL,
  `rel` varchar(255) NOT NULL,
  `hrefaddon` varchar(255) NOT NULL,
  `pagination` smallint(6) NOT NULL,
  `customlimit` smallint(6) NOT NULL,
  `catid` int(11) NOT NULL,
  `controls` tinyint(1) NOT NULL default 1,
  `youtubeparams` varchar(450) NOT NULL,
  `playertype` smallint(6) NOT NULL,
  `useglass` tinyint(1) NOT NULL default 0,
  `logocover` varchar(255) NOT NULL,
  `customlayout` text NOT NULL,
  `randomization` tinyint(1) NOT NULL default 0,
  `prepareheadtags` tinyint(1) NOT NULL default 0,
  `updateperiod` smallint(6) NOT NULL,
  `lastplaylistupdate` datetime NOT NULL,
  `muteonplay` tinyint(1) NOT NULL default 0,
  `volume` smallint(6) NOT NULL default -1,


  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__youtubegallery_categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `categoryname` varchar(50) NOT NULL,

  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__youtubegallery_videos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `galleryid` int(11) NOT NULL,
  `parentid` int(11) NOT NULL,
  `videosource` varchar(30) NOT NULL,
  `videoid` varchar(30) NOT NULL,
  `imageurl` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `custom_imageurl` varchar(255) NOT NULL,
  `custom_title` varchar(255) NOT NULL,
  `custom_description` text NOT NULL,
  `specialparams` varchar(255) NOT NULL,
  `lastupdate` datetime NOT NULL,
  `allowupdates` tinyint(1) NOT NULL default 0,
  `status` smallint(6) NOT NULL,
  `isvideo` tinyint(1) NOT NULL default 0,
  `link` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL default 0,

  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;