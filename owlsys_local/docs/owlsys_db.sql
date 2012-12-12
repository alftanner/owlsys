-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 11, 2012 at 07:22 PM
-- Server version: 5.5.24
-- PHP Version: 5.3.10-1ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `owlsys_db`
--
CREATE DATABASE `owlsys_db` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `owlsys_db`;

-- --------------------------------------------------------

--
-- Table structure for table `os_acl_account`
--

CREATE TABLE IF NOT EXISTS `os_acl_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `salt` char(50) NOT NULL,
  `registerdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lastvisitdate` timestamp NULL DEFAULT NULL,
  `block` int(1) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `os_acl_account`
--

INSERT INTO `os_acl_account` (`id`, `email`, `password`, `salt`, `registerdate`, `lastvisitdate`, `block`, `role_id`) VALUES
(1, 'admin@localhost.com', 'b6b0df48150be49376d2a73db4ed537e', 'ce8d96d579d389e783f95b3772785783ea1a9854', '2012-10-29 05:12:42', '2012-10-29 05:12:42', 0, 1),
(3, 'info@rogercastaneda.com', '0dbb449a40372b8db6f19c82af06b107', '0eaa51588ea0b611e7ce3196f0b3842b', '2012-10-18 01:15:53', NULL, 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `os_acl_permission`
--

CREATE TABLE IF NOT EXISTS `os_acl_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `privilege` varchar(5) NOT NULL DEFAULT 'deny',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=616 ;

--
-- Dumping data for table `os_acl_permission`
--

INSERT INTO `os_acl_permission` (`id`, `role_id`, `resource_id`, `privilege`) VALUES
(349, 2, 1, 'allow'),
(614, 3, 82, 'deny'),
(613, 3, 81, 'deny'),
(612, 3, 80, 'deny'),
(611, 3, 79, 'deny'),
(610, 3, 78, 'deny'),
(609, 3, 77, 'deny'),
(608, 3, 76, 'deny'),
(607, 3, 75, 'deny'),
(606, 3, 74, 'deny'),
(605, 3, 73, 'deny'),
(604, 3, 72, 'deny'),
(603, 3, 71, 'deny'),
(602, 3, 70, 'allow'),
(601, 3, 69, 'allow'),
(600, 3, 86, 'allow'),
(599, 3, 85, 'deny'),
(598, 3, 68, 'allow'),
(597, 3, 67, 'deny'),
(596, 3, 66, 'deny'),
(595, 3, 65, 'deny'),
(594, 3, 64, 'deny'),
(593, 3, 63, 'deny'),
(592, 3, 62, 'deny'),
(591, 3, 61, 'deny'),
(590, 3, 60, 'deny'),
(589, 3, 59, 'deny'),
(588, 3, 58, 'deny'),
(587, 3, 57, 'deny'),
(586, 3, 56, 'allow'),
(585, 3, 55, 'deny'),
(584, 3, 54, 'deny'),
(583, 3, 88, 'deny'),
(582, 3, 53, 'allow'),
(581, 3, 52, 'deny'),
(580, 3, 51, 'deny'),
(579, 3, 50, 'deny'),
(578, 3, 49, 'deny'),
(577, 3, 48, 'deny'),
(576, 3, 47, 'deny'),
(575, 3, 46, 'deny'),
(574, 3, 45, 'deny'),
(573, 3, 44, 'allow'),
(572, 3, 43, 'allow'),
(571, 3, 42, 'allow'),
(570, 3, 41, 'deny'),
(569, 3, 40, 'deny'),
(568, 3, 39, 'deny'),
(567, 3, 38, 'deny'),
(566, 3, 37, 'deny'),
(565, 3, 36, 'deny'),
(564, 3, 35, 'deny'),
(563, 3, 90, 'deny'),
(562, 3, 89, 'deny'),
(561, 3, 87, 'deny'),
(560, 3, 83, 'deny'),
(559, 3, 34, 'deny'),
(558, 3, 33, 'deny'),
(557, 3, 32, 'deny'),
(556, 3, 31, 'deny'),
(555, 3, 30, 'deny'),
(554, 3, 29, 'deny'),
(553, 3, 28, 'deny'),
(552, 3, 27, 'deny'),
(551, 3, 26, 'deny'),
(550, 3, 91, 'allow'),
(549, 3, 25, 'deny'),
(548, 3, 24, 'deny'),
(547, 3, 23, 'deny'),
(546, 3, 22, 'deny'),
(545, 3, 21, 'allow'),
(544, 3, 20, 'allow'),
(543, 3, 19, 'deny'),
(542, 3, 18, 'deny'),
(541, 3, 17, 'deny'),
(540, 3, 16, 'deny'),
(539, 3, 15, 'deny'),
(538, 3, 14, 'deny'),
(537, 3, 13, 'deny'),
(536, 3, 12, 'deny'),
(535, 3, 11, 'deny'),
(534, 3, 10, 'deny'),
(533, 3, 9, 'deny'),
(532, 3, 8, 'deny'),
(531, 3, 7, 'deny'),
(530, 3, 6, 'deny'),
(529, 3, 5, 'deny'),
(350, 2, 2, 'allow'),
(351, 2, 3, 'allow'),
(352, 2, 4, 'deny'),
(353, 2, 5, 'deny'),
(354, 2, 6, 'allow'),
(355, 2, 7, 'deny'),
(356, 2, 8, 'deny'),
(357, 2, 9, 'deny'),
(358, 2, 10, 'deny'),
(359, 2, 11, 'deny'),
(360, 2, 12, 'deny'),
(361, 2, 13, 'deny'),
(362, 2, 14, 'deny'),
(363, 2, 15, 'deny'),
(364, 2, 16, 'deny'),
(365, 2, 17, 'deny'),
(366, 2, 18, 'deny'),
(367, 2, 19, 'deny'),
(368, 2, 20, 'allow'),
(369, 2, 21, 'allow'),
(370, 2, 22, 'deny'),
(371, 2, 23, 'deny'),
(372, 2, 24, 'deny'),
(373, 2, 25, 'deny'),
(374, 2, 26, 'deny'),
(375, 2, 27, 'deny'),
(376, 2, 28, 'deny'),
(377, 2, 29, 'deny'),
(378, 2, 30, 'deny'),
(379, 2, 31, 'deny'),
(380, 2, 32, 'deny'),
(381, 2, 33, 'deny'),
(382, 2, 34, 'deny'),
(383, 2, 83, 'deny'),
(384, 2, 35, 'deny'),
(385, 2, 36, 'deny'),
(386, 2, 37, 'deny'),
(387, 2, 38, 'deny'),
(388, 2, 39, 'deny'),
(389, 2, 40, 'deny'),
(390, 2, 41, 'deny'),
(391, 2, 42, 'allow'),
(392, 2, 43, 'allow'),
(393, 2, 44, 'allow'),
(394, 2, 45, 'deny'),
(395, 2, 46, 'deny'),
(396, 2, 47, 'deny'),
(397, 2, 48, 'deny'),
(398, 2, 49, 'deny'),
(399, 2, 50, 'deny'),
(400, 2, 51, 'deny'),
(401, 2, 52, 'deny'),
(402, 2, 53, 'allow'),
(403, 2, 54, 'deny'),
(404, 2, 55, 'deny'),
(405, 2, 56, 'allow'),
(406, 2, 57, 'deny'),
(407, 2, 58, 'deny'),
(408, 2, 59, 'deny'),
(409, 2, 60, 'deny'),
(410, 2, 61, 'deny'),
(411, 2, 62, 'deny'),
(412, 2, 63, 'deny'),
(413, 2, 64, 'deny'),
(414, 2, 65, 'deny'),
(415, 2, 66, 'deny'),
(416, 2, 67, 'deny'),
(417, 2, 68, 'allow'),
(418, 2, 85, 'deny'),
(419, 2, 86, 'allow'),
(420, 2, 69, 'allow'),
(421, 2, 70, 'allow'),
(422, 2, 71, 'deny'),
(423, 2, 72, 'deny'),
(424, 2, 73, 'deny'),
(425, 2, 74, 'deny'),
(426, 2, 75, 'deny'),
(427, 2, 76, 'deny'),
(428, 2, 77, 'deny'),
(429, 2, 78, 'deny'),
(430, 2, 79, 'deny'),
(431, 2, 80, 'deny'),
(432, 2, 81, 'deny'),
(433, 2, 82, 'deny'),
(434, 2, 84, 'allow'),
(528, 3, 4, 'deny'),
(527, 3, 3, 'allow'),
(526, 3, 2, 'allow'),
(525, 3, 1, 'allow'),
(615, 3, 84, 'allow');

-- --------------------------------------------------------

--
-- Table structure for table `os_acl_resource`
--

CREATE TABLE IF NOT EXISTS `os_acl_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(40) NOT NULL,
  `controller` varchar(40) NOT NULL,
  `actioncontroller` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=92 ;

--
-- Dumping data for table `os_acl_resource`
--

INSERT INTO `os_acl_resource` (`id`, `module`, `controller`, `actioncontroller`) VALUES
(1, 'default', 'Index', 'index'),
(2, 'default', 'Error', 'error'),
(3, 'default', 'Error', 'noauth'),
(4, 'acl', 'Account', 'index'),
(5, 'acl', 'Account', 'create'),
(6, 'acl', 'Account', 'update'),
(7, 'acl', 'Account', 'list'),
(8, 'acl', 'Account', 'delete'),
(9, 'acl', 'Account', 'block'),
(10, 'acl', 'Resource', 'index'),
(11, 'acl', 'Resource', 'sync'),
(12, 'acl', 'Resource', 'list'),
(13, 'acl', 'Resource', 'delete'),
(14, 'acl', 'Role', 'index'),
(15, 'acl', 'Role', 'list'),
(16, 'acl', 'Role', 'create'),
(17, 'acl', 'Role', 'update'),
(18, 'acl', 'Role', 'delete'),
(19, 'acl', 'Authentication', 'index'),
(20, 'acl', 'Authentication', 'login'),
(21, 'acl', 'Authentication', 'logout'),
(22, 'acl', 'Index', 'index'),
(23, 'acl', 'Permission', 'index'),
(24, 'acl', 'Permission', 'manage'),
(25, 'acl', 'Permission', 'update'),
(26, 'system', 'Index', 'index'),
(27, 'system', 'Widget', 'index'),
(28, 'system', 'Widget', 'choose'),
(29, 'system', 'Widget', 'list'),
(30, 'system', 'Widget', 'new'),
(31, 'system', 'Widget', 'update'),
(32, 'system', 'Widget', 'publish'),
(33, 'system', 'Widget', 'delete'),
(34, 'system', 'Widget', 'move'),
(35, 'menu', 'Index', 'index'),
(36, 'menu', 'Menu', 'index'),
(37, 'menu', 'Menu', 'create'),
(38, 'menu', 'Menu', 'update'),
(39, 'menu', 'Menu', 'delete'),
(40, 'menu', 'Menu', 'list'),
(41, 'menu', 'Menu', 'publish'),
(42, 'menu', 'Menu', 'render'),
(43, 'menu', 'Menu', 'breadcrumb'),
(44, 'menu', 'Menu', 'sitemap'),
(45, 'menu', 'Item', 'index'),
(46, 'menu', 'Item', 'list'),
(47, 'menu', 'Item', 'add'),
(48, 'menu', 'Item', 'move'),
(49, 'menu', 'Item', 'update'),
(50, 'menu', 'Item', 'publish'),
(51, 'menu', 'Item', 'delete'),
(52, 'menu', 'Item', 'choose'),
(53, 'menu', 'Item', 'weblink'),
(54, 'content', 'Article', 'index'),
(55, 'content', 'Article', 'listregistered'),
(56, 'content', 'Article', 'add'),
(57, 'content', 'Article', 'edit'),
(58, 'content', 'Article', 'move'),
(59, 'content', 'Article', 'publish'),
(60, 'content', 'Article', 'delete'),
(61, 'content', 'Category', 'index'),
(62, 'content', 'Category', 'add'),
(63, 'content', 'Category', 'edit'),
(64, 'content', 'Category', 'move'),
(65, 'content', 'Category', 'publish'),
(66, 'content', 'Category', 'delete'),
(67, 'content', 'Category', 'listregistered'),
(68, 'content', 'Article', 'view'),
(69, 'contact', 'Contact', 'index'),
(70, 'contact', 'Contact', 'view'),
(71, 'contact', 'Contact', 'listregistered'),
(72, 'contact', 'Contact', 'add'),
(73, 'contact', 'Contact', 'edit'),
(74, 'contact', 'Contact', 'publish'),
(75, 'contact', 'Contact', 'remove'),
(76, 'contact', 'Contact', 'move'),
(77, 'contact', 'Category', 'index'),
(78, 'contact', 'Category', 'listregistered'),
(79, 'contact', 'Category', 'add'),
(80, 'contact', 'Category', 'edit'),
(81, 'contact', 'Category', 'publish'),
(82, 'contact', 'Category', 'remove'),
(83, 'system', 'Index', 'extension'),
(84, 'contact', 'Category', 'view'),
(85, 'content', 'Article', 'getbychar'),
(86, 'content', 'Category', 'viewbloglayout'),
(87, 'system', 'Skin', 'index'),
(88, 'menu', 'Menu', 'renderbootstrap'),
(89, 'system', 'Skin', 'list'),
(90, 'system', 'Skin', 'select'),
(91, 'acl', 'Authentication', 'validate');

-- --------------------------------------------------------

--
-- Table structure for table `os_acl_role`
--

CREATE TABLE IF NOT EXISTS `os_acl_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `desktop_layout` varchar(100) NOT NULL DEFAULT 'frontend',
  `mobile_layout` varchar(100) NOT NULL DEFAULT 'frontend_mobile',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `os_acl_role`
--

INSERT INTO `os_acl_role` (`id`, `name`, `parent_id`, `priority`, `desktop_layout`, `mobile_layout`) VALUES
(1, 'root', 0, 0, 'backend', 'backend_mobile'),
(2, 'Administrator', 1, 1, 'backend', 'backend_mobile'),
(3, 'Guest', 2, 1, 'frontend', 'frontend_mobile');

-- --------------------------------------------------------

--
-- Table structure for table `os_contact`
--

CREATE TABLE IF NOT EXISTS `os_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `con_position` varchar(255) DEFAULT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postcode` varchar(100) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `misc` mediumtext,
  `image` varchar(255) DEFAULT NULL,
  `email_to` varchar(255) DEFAULT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `account_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `webpage` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `os_contact_category`
--

CREATE TABLE IF NOT EXISTS `os_contact_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `os_content_article`
--

CREATE TABLE IF NOT EXISTS `os_content_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `introcontent` text NOT NULL,
  `content` text NOT NULL,
  `published` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `os_content_category`
--

CREATE TABLE IF NOT EXISTS `os_content_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `published` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `os_menu`
--

CREATE TABLE IF NOT EXISTS `os_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `os_menu`
--

INSERT INTO `os_menu` (`id`, `name`, `published`) VALUES
(1, 'Admin Main Menu', 1);

-- --------------------------------------------------------

--
-- Table structure for table `os_menu_item`
--

CREATE TABLE IF NOT EXISTS `os_menu_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_alias` varchar(50) DEFAULT NULL COMMENT 'Id para ser identificado a traves de zend navigation',
  `menu_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL COMMENT 'order of the item',
  `icon` varchar(20) DEFAULT NULL,
  `wtype` varchar(10) NOT NULL DEFAULT '_self' COMMENT 'window type (_self, _blank, _parent)',
  `params` text,
  `published` tinyint(1) NOT NULL,
  `title` varchar(50) NOT NULL COMMENT 'Link label',
  `description` varchar(150) DEFAULT NULL,
  `external` tinyint(4) NOT NULL DEFAULT '0',
  `mid` int(11) NOT NULL,
  `isvisible` int(11) NOT NULL,
  `css_class` varchar(50) DEFAULT NULL,
  `depth` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `os_menu_item`
--

INSERT INTO `os_menu_item` (`id`, `id_alias`, `menu_id`, `resource_id`, `parent_id`, `ordering`, `icon`, `wtype`, `params`, `published`, `title`, `description`, `external`, `mid`, `isvisible`, `css_class`, `depth`) VALUES
(1, '', 1, 7, 15, 3, NULL, '_self', '', 1, 'Accounts', 'Manage accounts', 0, 1, 1, '', 2),
(2, '', 1, 15, 15, 4, NULL, '_self', '', 1, 'Roles', 'Manage roles', 0, 3, 1, '', 2),
(3, '', 1, 40, 6, 6, NULL, '_self', '', 1, 'Menus', 'Manage menus', 0, 1, 1, '', 2),
(4, '', 1, 12, 6, 5, NULL, '_self', '', 1, 'Resources', 'Manage resources', 0, 5, 1, '', 2),
(5, 'widgets', 1, 29, 0, 7, NULL, '_self', '', 1, 'Widgets', 'Manage widgets', 0, 1, 1, '', 1),
(6, 'system', 1, 26, 0, 1, NULL, '_self', '', 1, 'System', 'Show system info like version, license and others.', 0, 3, 1, '', 1),
(7, '', 1, 55, 16, 2, NULL, '_self', '', 1, 'Content', '', 0, 1, 1, '', 2),
(8, '', 1, 56, 7, 1, NULL, '_self', '', 1, 'Add Article', '', 0, 2, 1, NULL, 3),
(9, '', 1, 67, 7, 1, NULL, '_self', '', 1, 'Categories', '', 0, 3, 1, NULL, 3),
(10, '', 1, 62, 9, 1, NULL, '_self', '', 1, 'Add Category', '', 0, 4, 1, NULL, 3),
(15, '', 1, 7, 0, 2, NULL, '_self', '', 1, 'ACL', '', 0, 1, 1, '', 1),
(16, '', 1, 83, 0, 8, NULL, '_self', '', 1, 'Extensions', 'Show extensions availables in the system', 0, 3, 1, '', 1),
(17, '', 1, 71, 16, 3, NULL, '_self', '', 1, 'Contact', 'Contact Extension', 0, 1, 1, '', 2),
(18, '', 1, 72, 17, 9, NULL, '_self', '', 1, 'Add Contact', 'Display Add Contact Form in backend', 0, 2, 1, '', 3),
(19, '', 1, 78, 17, 10, NULL, '_self', '', 1, 'Contact categories', 'Display contact categories registered', 0, 3, 1, '', 3),
(20, '', 1, 79, 19, 1, NULL, '_self', '', 1, 'Add Category', 'Display form for add new contact category ', 0, 4, 1, '', 4),
(21, '', 1, 89, 6, 7, NULL, '_self', '', 1, 'Skins', '', 0, 5, 1, '', 2);

-- --------------------------------------------------------

--
-- Table structure for table `os_skin`
--

CREATE TABLE IF NOT EXISTS `os_skin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `isselected` int(11) NOT NULL,
  `author` varchar(100) NOT NULL,
  `license` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `os_skin`
--

INSERT INTO `os_skin` (`id`, `name`, `description`, `isselected`, `author`, `license`) VALUES
(1, 'default', 'Default skin for owlsys', 1, 'Roger Castaneda', 'GPL 2 or later');

-- --------------------------------------------------------

--
-- Table structure for table `os_widget`
--

CREATE TABLE IF NOT EXISTS `os_widget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(30) NOT NULL,
  `title` varchar(100) NOT NULL,
  `published` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `params` text NOT NULL,
  `resource_id` int(11) NOT NULL,
  `widget_id` int(11) NOT NULL,
  `showtitle` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `os_widget`
--

INSERT INTO `os_widget` (`id`, `position`, `title`, `published`, `ordering`, `params`, `resource_id`, `widget_id`, `showtitle`) VALUES
(5, 'pos_admin_top_right', 'Login Module', 1, 1, 'renderfor=0\nheadertitle=\nfootermessage=\nismodule=1', 20, 1, 0),
(3, 'breadcrumb', 'Breadcrumb', 1, 1, 'renderfor=0\ndepth=0\nlastlink=0\nseparator=Â»', 43, 2, 0),
(4, 'pos_admin_mainmenu', 'Admin Main Menu', 1, 1, 'renderfor=0\ntoken=e03f07d9af3b0805bfcdf7219ebe7f1b\nmenuId=1\ndistribution=horizontal', 88, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `os_widget_detail`
--

CREATE TABLE IF NOT EXISTS `os_widget_detail` (
  `widget_id` int(11) NOT NULL,
  `menuitem_id` int(11) NOT NULL,
  PRIMARY KEY (`widget_id`,`menuitem_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `os_widget_detail`
--

INSERT INTO `os_widget_detail` (`widget_id`, `menuitem_id`) VALUES
(3, 0),
(4, 0),
(5, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
