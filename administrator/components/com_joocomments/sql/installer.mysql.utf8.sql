
-- v1.0.0
-- Friday Sep-02-2011
-- @component JooComments
-- @copyright Copyright (C) Abhiram Mishra www.bullraider.com
-- @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;

--
-- Table structure for table `jos_joocomments`
--

CREATE TABLE IF NOT EXISTS `#__joocomments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(200) DEFAULT NULL,
  `comment` text NOT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `website` varchar(100) NOT NULL DEFAULT '',
  `publish_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;