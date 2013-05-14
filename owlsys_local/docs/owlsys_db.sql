-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 13, 2013 at 11:19 PM
-- Server version: 5.5.31
-- PHP Version: 5.3.10-1ubuntu3.6

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `owlsys_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `os_acl_account`
--
-- Creation: May 14, 2013 at 03:13 AM
--

CREATE TABLE IF NOT EXISTS `os_acl_account` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) CHARACTER SET latin1 NOT NULL,
  `password` varchar(100) CHARACTER SET latin1 NOT NULL,
  `registerdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastvisitdate` timestamp NULL DEFAULT NULL,
  `block` int(1) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL,
  `fullname` varchar(200) CHARACTER SET latin1 NOT NULL,
  `email_alternative` varchar(100) CHARACTER SET latin1 NOT NULL,
  `recoverpwdtoken` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `email_alternative` (`email_alternative`),
  KEY `fullname` (`fullname`),
  KEY `FK_os_acl_account` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- RELATIONS FOR TABLE `os_acl_account`:
--   `role_id`
--       `os_acl_role` -> `id`
--

--
-- Dumping data for table `os_acl_account`
--

INSERT INTO `os_acl_account` (`id`, `email`, `password`, `registerdate`, `lastvisitdate`, `block`, `role_id`, `fullname`, `email_alternative`, `recoverpwdtoken`) VALUES
(1, 'rogercastanedag@gmail.com', '$6$5000$JUuXJbDOV1j/QIdftNecmNfMJV7iXiLOfzHfPl4CBqEKM4i3TAKgyjmCo4l455RZOVwLqctCx/hFNJlLWm.60/', '2013-01-15 13:37:10', '2013-05-14 02:08:37', 0, 1, 'System Administrator', 'admin@localhost.com', ''),
(3, 'info@rogercastaneda.com', '$6$5000$OYrwSna43yFmglD8stOWnaiXgelrv9RZp7wKusp4x7DGoKOVOjeW5tz12Vxgkc9iNQQQHKeyR/S1DmDJoO8BI/', '2012-10-18 08:15:53', NULL, 0, 2, 'Roger Castaneda', 'info@rogercastaneda.com', '');

-- --------------------------------------------------------

--
-- Table structure for table `os_acl_permission`
--
-- Creation: May 14, 2013 at 03:13 AM
--

CREATE TABLE IF NOT EXISTS `os_acl_permission` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `privilege` varchar(5) CHARACTER SET latin1 NOT NULL DEFAULT 'deny',
  PRIMARY KEY (`id`),
  KEY `FK_os_acl_permission` (`role_id`),
  KEY `FK_os_acl_permission_resource` (`resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- RELATIONS FOR TABLE `os_acl_permission`:
--   `role_id`
--       `os_acl_role` -> `id`
--   `resource_id`
--       `os_acl_resource` -> `id`
--

--
-- Dumping data for table `os_acl_permission`
--

INSERT INTO `os_acl_permission` (`id`, `role_id`, `resource_id`, `privilege`) VALUES
(1, 2, 1, 'allow'),
(2, 3, 70, 'allow'),
(3, 3, 69, 'allow'),
(4, 3, 86, 'allow'),
(5, 3, 68, 'allow'),
(6, 3, 56, 'allow'),
(7, 3, 53, 'allow'),
(8, 3, 44, 'allow'),
(9, 3, 43, 'allow'),
(10, 3, 42, 'allow'),
(11, 3, 91, 'allow'),
(12, 3, 21, 'allow'),
(13, 3, 20, 'allow'),
(14, 2, 2, 'allow'),
(15, 2, 3, 'allow'),
(16, 2, 6, 'allow'),
(17, 2, 20, 'allow'),
(18, 2, 21, 'allow'),
(19, 2, 42, 'allow'),
(20, 2, 43, 'allow'),
(21, 2, 44, 'allow'),
(22, 2, 53, 'allow'),
(23, 2, 56, 'allow'),
(24, 2, 68, 'allow'),
(25, 2, 86, 'allow'),
(26, 2, 69, 'allow'),
(27, 2, 70, 'allow'),
(28, 2, 84, 'allow'),
(29, 3, 3, 'allow'),
(30, 3, 2, 'allow'),
(31, 3, 1, 'allow'),
(32, 3, 84, 'allow');

-- --------------------------------------------------------

--
-- Table structure for table `os_acl_resource`
--
-- Creation: May 14, 2013 at 03:13 AM
--

CREATE TABLE IF NOT EXISTS `os_acl_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(40) CHARACTER SET latin1 NOT NULL,
  `controller` varchar(40) CHARACTER SET latin1 NOT NULL,
  `actioncontroller` varchar(40) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module` (`module`,`controller`,`actioncontroller`),
  KEY `controller` (`controller`),
  KEY `module_2` (`module`),
  KEY `actioncontroller` (`actioncontroller`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=380 ;

--
-- Dumping data for table `os_acl_resource`
--

INSERT INTO `os_acl_resource` (`id`, `module`, `controller`, `actioncontroller`) VALUES
(9, 'acl', 'Account', 'block'),
(5, 'acl', 'Account', 'create'),
(8, 'acl', 'Account', 'delete'),
(93, 'acl', 'Account', 'edit'),
(4, 'acl', 'Account', 'index'),
(7, 'acl', 'Account', 'list'),
(308, 'acl', 'Account', 'resetpassword'),
(6, 'acl', 'Account', 'update'),
(19, 'acl', 'Authentication', 'index'),
(20, 'acl', 'Authentication', 'login'),
(21, 'acl', 'Authentication', 'logout'),
(91, 'acl', 'Authentication', 'validate'),
(22, 'acl', 'Index', 'index'),
(23, 'acl', 'Permission', 'index'),
(24, 'acl', 'Permission', 'manage'),
(25, 'acl', 'Permission', 'update'),
(13, 'acl', 'Resource', 'delete'),
(10, 'acl', 'Resource', 'index'),
(12, 'acl', 'Resource', 'list'),
(11, 'acl', 'Resource', 'sync'),
(16, 'acl', 'Role', 'create'),
(18, 'acl', 'Role', 'delete'),
(14, 'acl', 'Role', 'index'),
(15, 'acl', 'Role', 'list'),
(17, 'acl', 'Role', 'update'),
(79, 'contact', 'Category', 'add'),
(80, 'contact', 'Category', 'edit'),
(77, 'contact', 'Category', 'index'),
(78, 'contact', 'Category', 'listregistered'),
(81, 'contact', 'Category', 'publish'),
(82, 'contact', 'Category', 'remove'),
(84, 'contact', 'Category', 'view'),
(72, 'contact', 'Contact', 'add'),
(73, 'contact', 'Contact', 'edit'),
(69, 'contact', 'Contact', 'index'),
(71, 'contact', 'Contact', 'listregistered'),
(76, 'contact', 'Contact', 'move'),
(74, 'contact', 'Contact', 'publish'),
(75, 'contact', 'Contact', 'remove'),
(70, 'contact', 'Contact', 'view'),
(56, 'content', 'Article', 'add'),
(60, 'content', 'Article', 'delete'),
(57, 'content', 'Article', 'edit'),
(85, 'content', 'Article', 'getbychar'),
(54, 'content', 'Article', 'index'),
(55, 'content', 'Article', 'listregistered'),
(58, 'content', 'Article', 'move'),
(59, 'content', 'Article', 'publish'),
(68, 'content', 'Article', 'view'),
(62, 'content', 'Category', 'add'),
(66, 'content', 'Category', 'delete'),
(63, 'content', 'Category', 'edit'),
(61, 'content', 'Category', 'index'),
(67, 'content', 'Category', 'listregistered'),
(64, 'content', 'Category', 'move'),
(65, 'content', 'Category', 'publish'),
(86, 'content', 'Category', 'viewbloglayout'),
(2, 'default', 'Error', 'error'),
(92, 'default', 'Error', 'ids'),
(3, 'default', 'Error', 'noauth'),
(1, 'default', 'Index', 'index'),
(35, 'menu', 'Index', 'index'),
(47, 'menu', 'Item', 'add'),
(52, 'menu', 'Item', 'choose'),
(51, 'menu', 'Item', 'delete'),
(45, 'menu', 'Item', 'index'),
(46, 'menu', 'Item', 'list'),
(48, 'menu', 'Item', 'move'),
(50, 'menu', 'Item', 'publish'),
(49, 'menu', 'Item', 'update'),
(53, 'menu', 'Item', 'weblink'),
(43, 'menu', 'Menu', 'breadcrumb'),
(37, 'menu', 'Menu', 'create'),
(39, 'menu', 'Menu', 'delete'),
(36, 'menu', 'Menu', 'index'),
(40, 'menu', 'Menu', 'list'),
(41, 'menu', 'Menu', 'publish'),
(42, 'menu', 'Menu', 'render'),
(88, 'menu', 'Menu', 'renderbootstrap'),
(44, 'menu', 'Menu', 'sitemap'),
(38, 'menu', 'Menu', 'update'),
(83, 'system', 'Index', 'extension'),
(26, 'system', 'Index', 'index'),
(87, 'system', 'Skin', 'index'),
(89, 'system', 'Skin', 'list'),
(90, 'system', 'Skin', 'select'),
(28, 'system', 'Widget', 'choose'),
(33, 'system', 'Widget', 'delete'),
(27, 'system', 'Widget', 'index'),
(29, 'system', 'Widget', 'list'),
(34, 'system', 'Widget', 'move'),
(30, 'system', 'Widget', 'new'),
(32, 'system', 'Widget', 'publish'),
(31, 'system', 'Widget', 'update');

-- --------------------------------------------------------

--
-- Table structure for table `os_acl_role`
--
-- Creation: May 14, 2013 at 03:13 AM
--

CREATE TABLE IF NOT EXISTS `os_acl_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `priority` int(11) NOT NULL,
  `desktop_layout` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'frontend',
  `mobile_layout` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'frontend_mobile',
  PRIMARY KEY (`id`),
  KEY `FK_os_acl_role` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- RELATIONS FOR TABLE `os_acl_role`:
--   `parent_id`
--       `os_acl_role` -> `id`
--

--
-- Dumping data for table `os_acl_role`
--

INSERT INTO `os_acl_role` (`id`, `name`, `parent_id`, `priority`, `desktop_layout`, `mobile_layout`) VALUES
(1, 'root', NULL, 0, 'backend', 'backend_mobile'),
(2, 'Administrator', 1, 1, 'backend', 'backend_mobile'),
(3, 'Guest', 2, 1, 'frontend', 'frontend_mobile');

-- --------------------------------------------------------

--
-- Table structure for table `os_contact`
--
-- Creation: May 14, 2013 at 03:13 AM
-- Last update: May 14, 2013 at 03:13 AM
--

CREATE TABLE IF NOT EXISTS `os_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `con_position` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `address` text CHARACTER SET latin1,
  `city` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `country` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `postcode` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `telephone` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `fax` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `misc` mediumtext CHARACTER SET latin1,
  `image` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `email_to` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `account_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `mobile` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `webpage` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `os_contact_category`
--
-- Creation: May 14, 2013 at 03:13 AM
-- Last update: May 14, 2013 at 03:13 AM
--

CREATE TABLE IF NOT EXISTS `os_contact_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL,
  `image` varchar(255) CHARACTER SET latin1 NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `os_content_article`
--
-- Creation: May 14, 2013 at 03:35 AM
-- Last update: May 14, 2013 at 03:35 AM
--

CREATE TABLE IF NOT EXISTS `os_content_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET latin1 NOT NULL,
  `introcontent` text CHARACTER SET latin1 NOT NULL,
  `content` text CHARACTER SET latin1 NOT NULL,
  `published` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `os_content_category`
--
-- Creation: May 14, 2013 at 03:36 AM
-- Last update: May 14, 2013 at 03:36 AM
--

CREATE TABLE IF NOT EXISTS `os_content_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL,
  `published` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `os_menu`
--
-- Creation: May 14, 2013 at 03:13 AM
--

CREATE TABLE IF NOT EXISTS `os_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `os_menu`
--

INSERT INTO `os_menu` (`id`, `name`, `published`) VALUES
(1, 'Admin Main Menu', 1);

-- --------------------------------------------------------

--
-- Table structure for table `os_menu_item`
--
-- Creation: May 14, 2013 at 03:13 AM
--

CREATE TABLE IF NOT EXISTS `os_menu_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_alias` varchar(50) CHARACTER SET latin1 DEFAULT NULL COMMENT 'Id para ser identificado a traves de zend navigation',
  `menu_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT '0',
  `ordering` int(11) NOT NULL COMMENT 'order of the item',
  `icon` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `wtype` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT '_self' COMMENT 'window type (_self, _blank, _parent)',
  `params` text CHARACTER SET latin1,
  `published` tinyint(1) NOT NULL,
  `title` varchar(50) CHARACTER SET latin1 NOT NULL COMMENT 'Link label',
  `description` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
  `external` tinyint(4) NOT NULL DEFAULT '0',
  `mid` int(11) NOT NULL,
  `isvisible` int(11) NOT NULL,
  `css_class` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `depth` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_os_menu` (`menu_id`),
  KEY `FK_os_menu_item_parent` (`parent_id`),
  KEY `FK_menu_item_resource` (`resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=127 ;

--
-- RELATIONS FOR TABLE `os_menu_item`:
--   `resource_id`
--       `os_acl_resource` -> `id`
--   `menu_id`
--       `os_menu` -> `id`
--   `parent_id`
--       `os_menu_item` -> `id`
--

--
-- Dumping data for table `os_menu_item`
--

INSERT INTO `os_menu_item` (`id`, `id_alias`, `menu_id`, `resource_id`, `parent_id`, `ordering`, `icon`, `wtype`, `params`, `published`, `title`, `description`, `external`, `mid`, `isvisible`, `css_class`, `depth`) VALUES
(1, '', 1, 7, 15, 3, NULL, '_self', '', 1, 'Accounts', 'Manage accounts', 0, 1, 1, '', 2),
(2, '', 1, 15, 15, 4, NULL, '_self', '', 1, 'Roles', 'Manage roles', 0, 3, 1, '', 2),
(3, '', 1, 40, 6, 6, NULL, '_self', '', 1, 'Menus', 'Manage menus', 0, 1, 1, '', 2),
(4, '', 1, 12, 6, 5, NULL, '_self', '', 1, 'Resources', 'Manage resources', 0, 5, 1, '', 2),
(5, 'widgets', 1, 29, NULL, 7, NULL, '_self', '', 1, 'Widgets', 'Manage widgets', 0, 1, 1, '', 1),
(6, 'system', 1, 26, NULL, 1, NULL, '_self', '', 1, 'System', 'Show system info like version, license and others.', 0, 3, 1, '', 1),
(7, '', 1, 55, 16, 2, NULL, '_self', '', 1, 'Content', '', 0, 1, 1, '', 2),
(8, '', 1, 56, 7, 1, NULL, '_self', '', 1, 'Add Article', '', 0, 2, 1, NULL, 3),
(9, '', 1, 67, 7, 1, NULL, '_self', '', 1, 'Categories', '', 0, 3, 1, NULL, 3),
(10, '', 1, 62, 9, 1, NULL, '_self', '', 1, 'Add Category', '', 0, 4, 1, NULL, 3),
(15, '', 1, 7, NULL, 2, NULL, '_self', '', 1, 'ACL', '', 0, 1, 1, '', 1),
(16, '', 1, 83, NULL, 8, NULL, '_self', '', 1, 'Extensions', 'Show extensions availables in the system', 0, 3, 1, '', 1),
(17, '', 1, 71, 16, 3, NULL, '_self', '', 1, 'Contact', 'Contact Extension', 0, 1, 1, '', 2),
(18, '', 1, 72, 17, 9, NULL, '_self', '', 1, 'Add Contact', 'Display Add Contact Form in backend', 0, 2, 1, '', 3),
(19, '', 1, 78, 17, 10, NULL, '_self', '', 1, 'Contact categories', 'Display contact categories registered', 0, 3, 1, '', 3),
(20, '', 1, 79, 19, 1, NULL, '_self', '', 1, 'Add Category', 'Display form for add new contact category ', 0, 4, 1, '', 4),
(21, '', 1, 89, 6, 7, NULL, '_self', '', 1, 'Skins', '', 0, 5, 1, '', 2);

-- --------------------------------------------------------

--
-- Table structure for table `os_skin`
--
-- Creation: May 14, 2013 at 03:13 AM
-- Last update: May 14, 2013 at 03:13 AM
--

CREATE TABLE IF NOT EXISTS `os_skin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(200) CHARACTER SET latin1 NOT NULL,
  `isselected` int(11) NOT NULL,
  `author` varchar(100) CHARACTER SET latin1 NOT NULL,
  `license` varchar(100) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `os_skin`
--

INSERT INTO `os_skin` (`id`, `name`, `description`, `isselected`, `author`, `license`) VALUES
(1, 'default', 'Default skin for owlsys', 1, 'Roger Castaneda', 'GPL 2 or later');

-- --------------------------------------------------------

--
-- Table structure for table `os_widget`
--
-- Creation: May 14, 2013 at 03:13 AM
--

CREATE TABLE IF NOT EXISTS `os_widget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(30) CHARACTER SET latin1 NOT NULL,
  `title` varchar(100) CHARACTER SET latin1 NOT NULL,
  `published` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `params` text CHARACTER SET latin1 NOT NULL,
  `resource_id` int(11) NOT NULL,
  `widget_id` int(11) NOT NULL,
  `showtitle` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_widget_resource` (`resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- RELATIONS FOR TABLE `os_widget`:
--   `resource_id`
--       `os_acl_resource` -> `id`
--

--
-- Dumping data for table `os_widget`
--

INSERT INTO `os_widget` (`id`, `position`, `title`, `published`, `ordering`, `params`, `resource_id`, `widget_id`, `showtitle`) VALUES
(3, 'breadcrumb', 'Breadcrumb', 1, 1, '{"renderfor":"0","depth":"0","lastlink":"0","separator":"Â»"}', 43, 2, 0),
(4, 'pos_admin_mainmenu', 'Admin Main Menu', 1, 1, '{"renderfor":"0","menuId":"1","distribution":"horizontal"}', 88, 3, 0),
(5, 'pos_admin_top_right', 'Login Module', 1, 1, '{"renderfor":"0","headertitle":"","footermessage":""}', 20, 1, 0),
(6, 'menu_top', 'Main Menu', 1, 1, '{"renderfor":"0","menuId":"2","distribution":"horizontal","css":"nav nav-pills pull-right"}', 42, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `os_widget_detail`
--
-- Creation: May 14, 2013 at 03:13 AM
--

CREATE TABLE IF NOT EXISTS `os_widget_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `widget_id` int(11) NOT NULL,
  `menuitem_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menuitem_id` (`menuitem_id`),
  KEY `widget_id` (`widget_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- RELATIONS FOR TABLE `os_widget_detail`:
--   `menuitem_id`
--       `os_menu_item` -> `id`
--   `widget_id`
--       `os_widget` -> `id`
--

--
-- Dumping data for table `os_widget_detail`
--

INSERT INTO `os_widget_detail` (`id`, `widget_id`, `menuitem_id`) VALUES
(1, 3, NULL),
(2, 4, NULL),
(4, 6, NULL),
(20, 5, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `os_acl_account`
--
ALTER TABLE `os_acl_account`
  ADD CONSTRAINT `FK_os_acl_account` FOREIGN KEY (`role_id`) REFERENCES `os_acl_role` (`id`);

--
-- Constraints for table `os_acl_permission`
--
ALTER TABLE `os_acl_permission`
  ADD CONSTRAINT `FK_os_acl_permission` FOREIGN KEY (`role_id`) REFERENCES `os_acl_role` (`id`),
  ADD CONSTRAINT `FK_os_acl_permission_resource` FOREIGN KEY (`resource_id`) REFERENCES `os_acl_resource` (`id`);

--
-- Constraints for table `os_acl_role`
--
ALTER TABLE `os_acl_role`
  ADD CONSTRAINT `FK_os_acl_role` FOREIGN KEY (`parent_id`) REFERENCES `os_acl_role` (`id`);

--
-- Constraints for table `os_menu_item`
--
ALTER TABLE `os_menu_item`
  ADD CONSTRAINT `FK_menu_item_resource` FOREIGN KEY (`resource_id`) REFERENCES `os_acl_resource` (`id`),
  ADD CONSTRAINT `FK_os_menu` FOREIGN KEY (`menu_id`) REFERENCES `os_menu` (`id`),
  ADD CONSTRAINT `FK_os_menu_item_parent` FOREIGN KEY (`parent_id`) REFERENCES `os_menu_item` (`id`);

--
-- Constraints for table `os_widget`
--
ALTER TABLE `os_widget`
  ADD CONSTRAINT `FK_widget_resource` FOREIGN KEY (`resource_id`) REFERENCES `os_acl_resource` (`id`);

--
-- Constraints for table `os_widget_detail`
--
ALTER TABLE `os_widget_detail`
  ADD CONSTRAINT `FK_widget_detail_menuitem` FOREIGN KEY (`menuitem_id`) REFERENCES `os_menu_item` (`id`),
  ADD CONSTRAINT `FK_widget_detail_widget` FOREIGN KEY (`widget_id`) REFERENCES `os_widget` (`id`);
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
