--
-- Database: `formbuilder`
--

-- --------------------------------------------------------

--
-- Table structure for table `form_content`
--

CREATE TABLE IF NOT EXISTS `form_content` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` varchar(255) NOT NULL DEFAULT '',
  `form_form` text,
  `submit_fields` text,
  `form_labels` text,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Table structure for table `form_custom_style`
--

CREATE TABLE IF NOT EXISTS `form_custom_style` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` varchar(255) NOT NULL DEFAULT '',
  `form_style` mediumtext,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- --------------------------------------------------------

--
-- Table structure for table `form_data`
--

CREATE TABLE IF NOT EXISTS `form_data` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL DEFAULT '',
  `UID` varchar(128) NOT NULL DEFAULT '',
  `form_id` varchar(255) NOT NULL DEFAULT '',
  `field_name` varchar(255) NOT NULL DEFAULT '',
  `field_type` varchar(50) NOT NULL DEFAULT '',
  `field_value` text,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Table structure for table `form_data_datetimes`
--

CREATE TABLE IF NOT EXISTS `form_data_datetimes` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `UID` varchar(128) NOT NULL DEFAULT '',
  `form_id` varchar(255) NOT NULL DEFAULT '',
  `datetimes` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Table structure for table `form_files`
--

CREATE TABLE IF NOT EXISTS `form_files` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `UID` varchar(128) NOT NULL DEFAULT '',
  `form_id` varchar(255) NOT NULL DEFAULT '',
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `file_path` varchar(1024) NOT NULL DEFAULT '',
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Table structure for table `form_list`
--

CREATE TABLE IF NOT EXISTS `form_list` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `form_name` varchar(255) NOT NULL DEFAULT '',
  `form_title` varchar(255) NOT NULL DEFAULT '',
  `publish_type` varchar(10) NOT NULL DEFAULT '',
  `publish_groups` varchar(1024) NOT NULL DEFAULT '',
  `publish_status` varchar(5) NOT NULL DEFAULT '',
  `admin_users` varchar(255) NOT NULL DEFAULT '1',
  `form_note` text,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
ALTER TABLE `form_list` ADD COLUMN `form_genral_style` longtext;
ALTER TABLE `form_list` ADD `amount_form_submission` VARCHAR(6) NOT NULL DEFAULT '-1' COMMENT 'The amount of form submission is allowed (-1 = no limit)' AFTER `publish_status`; 
ALTER TABLE `form_list` ADD `publish_deps` VARCHAR(4) NOT NULL DEFAULT '' AFTER `publish_groups`;

-- --------------------------------------------------------

--
-- Table structure for table `form_tables`
--

CREATE TABLE IF NOT EXISTS `form_tables` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `UID` varchar(128) NOT NULL DEFAULT '',
  `form_id` varchar(255) NOT NULL DEFAULT '',
  `table_name` varchar(255) NOT NULL DEFAULT '',
  `table_data` longtext,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `organization_tree`
--
CREATE TABLE IF NOT EXISTS `organization_tree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `parent_id` varchar(64) NOT NULL DEFAULT '1',
  `dep_mngr_user_id` varchar(6) NOT NULL DEFAULT '',
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `publish_type`
--
CREATE TABLE IF NOT EXISTS `publish_type` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `publish_type`
--

INSERT INTO `publish_type` (`id`, `name`) VALUES
(1, 'Public'),
(2, 'Groups'),
(3, 'Public-Anonymously'),
(4, 'Groups-Anonymously'),
(5, 'departments'),
(6, 'departments-Anonymously');
-- --------------------------------------------------------

--
-- Table structure for table `registration_request`
--

CREATE TABLE IF NOT EXISTS `registration_request` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `activation_code` varchar(128) NOT NULL DEFAULT '',
  `is_confirm` varchar(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `setting_group` varchar(10) NOT NULL DEFAULT '',
  `setting_name` varchar(128) NOT NULL DEFAULT '',
  `setting_nik` varchar(128) NOT NULL DEFAULT '',
  `setting_value` varchar(255) NOT NULL DEFAULT '',
  `options` varchar(255) NOT NULL DEFAULT '',
  `note` text,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(250) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(200) NOT NULL DEFAULT '',
  `status` varchar(1) NOT NULL DEFAULT '0',
  `groups` varchar(255) NOT NULL DEFAULT '',
  `forgot_verify` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
ALTER TABLE `users` ADD `dep_id` VARCHAR(6) NOT NULL DEFAULT '' AFTER `groups`;
-- --------------------------------------------------------

--
-- Table structure for table `users_gropes`
--

CREATE TABLE IF NOT EXISTS `users_gropes` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL DEFAULT '',
  `group_status` varchar(1) NOT NULL DEFAULT '1',
  `admin_ids` varchar(255) NOT NULL DEFAULT '1',
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
