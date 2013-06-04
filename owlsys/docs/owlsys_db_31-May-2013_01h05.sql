-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 31, 2013 at 01:55 PM
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

CREATE TABLE IF NOT EXISTS `os_acl_account` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) CHARACTER SET latin1 NOT NULL,
  `password` varchar(100) CHARACTER SET latin1 NOT NULL,
  `registerdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastvisitdate` timestamp NULL DEFAULT NULL,
  `isBlocked` tinyint(1) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL,
  `fullname` varchar(200) CHARACTER SET latin1 NOT NULL,
  `email_alternative` varchar(100) CHARACTER SET latin1 NOT NULL,
  `recoverpwdtoken` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `email_alternative` (`email_alternative`),
  KEY `fullname` (`fullname`),
  KEY `FK_os_acl_account` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- RELATIONS FOR TABLE `os_acl_account`:
--   `role_id`
--       `os_acl_role` -> `id`
--

--
-- Dumping data for table `os_acl_account`
--

INSERT INTO `os_acl_account` (`id`, `email`, `password`, `registerdate`, `lastvisitdate`, `isBlocked`, `role_id`, `fullname`, `email_alternative`, `recoverpwdtoken`) VALUES
(1, 'admin@localhost.com', '$6$5000$Ewm34Ye4xEbrUh4ru4KObceFylUIVt6kwKLTa7CsqJbJ7hNahLSB3oIXmHGQtVc93zIb8Ry2KjrSuzrhTprbN/', '2013-01-15 13:37:10', '2013-05-31 17:49:13', 0, 1, 'system administrator', 'admin@localhost.com', ''),
(3, 'info@rogercastaneda.com', '$6$5000$OYrwSna43yFmglD8stOWnaiXgelrv9RZp7wKusp4x7DGoKOVOjeW5tz12Vxgkc9iNQQQHKeyR/S1DmDJoO8BI/', '2012-10-18 08:15:53', NULL, 0, 2, 'Roger Castaneda', 'info@rogercastaneda.com', '');

-- --------------------------------------------------------

--
-- Table structure for table `os_acl_permission`
--

CREATE TABLE IF NOT EXISTS `os_acl_permission` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `isAllowed` tinyint(1) NOT NULL DEFAULT '0',
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

INSERT INTO `os_acl_permission` (`id`, `role_id`, `resource_id`, `isAllowed`) VALUES
(1, 2, 1, 1),
(2, 3, 70, 1),
(3, 3, 69, 1),
(4, 3, 86, 1),
(5, 3, 68, 1),
(6, 3, 56, 1),
(7, 3, 53, 1),
(8, 3, 44, 1),
(9, 3, 43, 1),
(10, 3, 42, 1),
(11, 3, 91, 1),
(12, 3, 21, 1),
(13, 3, 20, 1),
(14, 2, 2, 1),
(15, 2, 3, 1),
(16, 2, 6, 1),
(17, 2, 20, 1),
(18, 2, 21, 1),
(19, 2, 42, 1),
(20, 2, 43, 1),
(21, 2, 44, 1),
(22, 2, 53, 1),
(23, 2, 56, 1),
(24, 2, 68, 1),
(25, 2, 86, 1),
(26, 2, 69, 1),
(27, 2, 70, 1),
(28, 2, 84, 1),
(29, 3, 3, 1),
(30, 3, 2, 1),
(31, 3, 1, 1),
(32, 3, 84, 1);

-- --------------------------------------------------------

--
-- Table structure for table `os_acl_resource`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=309 ;

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

CREATE TABLE IF NOT EXISTS `os_acl_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `layout_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_role_layout` (`layout_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- RELATIONS FOR TABLE `os_acl_role`:
--   `layout_id`
--       `os_layout` -> `id`
--

--
-- Dumping data for table `os_acl_role`
--

INSERT INTO `os_acl_role` (`id`, `name`, `layout_id`) VALUES
(1, 'root', 2),
(2, 'Administrator', 2),
(3, 'Guest', 1);

-- --------------------------------------------------------

--
-- Table structure for table `os_layout`
--

CREATE TABLE IF NOT EXISTS `os_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(150) NOT NULL,
  `isPublished` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `os_layout`
--

INSERT INTO `os_layout` (`id`, `name`, `description`, `isPublished`) VALUES
(1, 'frontend', 'Front-end layout', 1),
(2, 'backend', 'Backend layout', 1);

-- --------------------------------------------------------

--
-- Table structure for table `os_menu`
--

CREATE TABLE IF NOT EXISTS `os_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `isPublished` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `os_menu`
--

INSERT INTO `os_menu` (`id`, `name`, `isPublished`) VALUES
(1, 'Admin Main Menu', 1);

-- --------------------------------------------------------

--
-- Table structure for table `os_menu_item`
--

CREATE TABLE IF NOT EXISTS `os_menu_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route` varchar(50) CHARACTER SET latin1 DEFAULT NULL COMMENT 'Id para ser identificado a traves de zend navigation',
  `menu_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT '0',
  `ordering` int(11) NOT NULL COMMENT 'order of the item',
  `icon` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `wtype` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT '_self' COMMENT 'window type (_self, _blank, _parent)',
  `params` text CHARACTER SET latin1,
  `isPublished` tinyint(1) NOT NULL,
  `title` varchar(50) CHARACTER SET latin1 NOT NULL COMMENT 'Link label',
  `description` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
  `external` tinyint(4) NOT NULL DEFAULT '0',
  `mid` int(11) NOT NULL,
  `isVisible` int(11) NOT NULL,
  `css_class` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `depth` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_os_menu` (`menu_id`),
  KEY `FK_os_menu_item_parent` (`parent_id`),
  KEY `FK_menu_item_resource` (`resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

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

INSERT INTO `os_menu_item` (`id`, `route`, `menu_id`, `resource_id`, `parent_id`, `ordering`, `icon`, `wtype`, `params`, `isPublished`, `title`, `description`, `external`, `mid`, `isVisible`, `css_class`, `depth`) VALUES
(1, 'accounts', 1, 7, 15, 3, NULL, '_self', '[]', 1, 'Accounts', 'Manage accounts', 0, 1, 1, '', 2),
(2, 'roles', 1, 15, 15, 4, NULL, '_self', '[]', 1, 'Roles', 'Manage roles', 0, 3, 1, '', 2),
(3, 'menus', 1, 40, 6, 6, NULL, '_self', '[]', 1, 'Menus', 'Manage menus', 0, 1, 1, '', 2),
(4, 'resources', 1, 12, 6, 5, NULL, '_self', '[]', 1, 'Resources', 'Manage resources', 0, 5, 1, '', 2),
(5, 'widgets', 1, 29, NULL, 7, NULL, '_self', '', 1, 'Widgets', 'Manage widgets', 0, 1, 1, '', 1),
(6, 'system', 1, 26, NULL, 1, NULL, '_self', '', 1, 'System', 'Show system info like version, license and others.', 0, 3, 1, '', 1),
(15, 'accounts', 1, 7, NULL, 2, NULL, '_self', '[]', 1, 'ACL', '', 0, 1, 1, '', 1),
(16, 'extensions', 1, 83, NULL, 8, NULL, '_self', '[]', 1, 'Extensions', 'Show extensions availables in the system', 0, 3, 1, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `os_skin`
--

CREATE TABLE IF NOT EXISTS `os_skin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(200) CHARACTER SET latin1 NOT NULL,
  `isSelected` tinyint(1) NOT NULL,
  `author` varchar(100) CHARACTER SET latin1 NOT NULL,
  `license` varchar(100) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `os_skin`
--

INSERT INTO `os_skin` (`id`, `name`, `description`, `isSelected`, `author`, `license`) VALUES
(1, 'default', 'Default skin for owlsys', 1, 'Roger Castaneda', 'GPL 2 or later');

-- --------------------------------------------------------

--
-- Table structure for table `os_widget`
--

CREATE TABLE IF NOT EXISTS `os_widget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(30) CHARACTER SET latin1 NOT NULL,
  `title` varchar(100) CHARACTER SET latin1 NOT NULL,
  `isPublished` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  `params` text CHARACTER SET latin1 NOT NULL,
  `resource_id` int(11) NOT NULL,
  `wid` int(11) NOT NULL,
  `showtitle` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_widget_resource` (`resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- RELATIONS FOR TABLE `os_widget`:
--   `resource_id`
--       `os_acl_resource` -> `id`
--

--
-- Dumping data for table `os_widget`
--

INSERT INTO `os_widget` (`id`, `position`, `title`, `isPublished`, `ordering`, `params`, `resource_id`, `wid`, `showtitle`) VALUES
(3, 'breadcrumb', 'Breadcrumb', 1, 1, '{"renderfor":"0","depth":"0","lastlink":"0","separator":"Â»"}', 43, 2, 0),
(4, 'pos_admin_mainmenu', 'Admin Main Menu', 1, 1, '{"renderfor":"0","menuId":"1","distribution":"horizontal"}', 88, 3, 0),
(5, 'pos_admin_top_right', 'Login Module', 1, 1, '{"renderfor":"0","headertitle":"","footermessage":""}', 20, 1, 0),
(6, 'menu_top', 'Main Menu', 1, 1, '{"renderfor":"0","menuId":"1","distribution":"horizontal","css":"nav nav-pills pull-right"}', 42, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `os_widget_detail`
--

CREATE TABLE IF NOT EXISTS `os_widget_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `widget_id` int(11) NOT NULL,
  `menuitem_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menuitem_id` (`menuitem_id`),
  KEY `widget_id` (`widget_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

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
(20, 5, NULL),
(21, 6, NULL);

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
  ADD CONSTRAINT `fk_role_layout` FOREIGN KEY (`layout_id`) REFERENCES `os_layout` (`id`);

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
