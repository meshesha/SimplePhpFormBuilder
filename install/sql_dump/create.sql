--
-- Database: `formbuilder`
--
-- --------------------------------------------------------

--
-- Table structure for table `form_content`
--

DROP TABLE IF EXISTS `form_content`;
CREATE TABLE IF NOT EXISTS `form_content` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` varchar(255) NOT NULL DEFAULT '',
  `form_form` text,
  `submit_fields` text,
  `form_labels` text,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `form_custom_style`
--

DROP TABLE IF EXISTS `form_custom_style`;
CREATE TABLE IF NOT EXISTS `form_custom_style` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` varchar(255) NOT NULL DEFAULT '',
  `form_style` mediumtext,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `form_data`
--

DROP TABLE IF EXISTS `form_data`;
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

DROP TABLE IF EXISTS `form_data_datetimes`;
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

DROP TABLE IF EXISTS `form_files`;
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

DROP TABLE IF EXISTS `form_list`;
CREATE TABLE IF NOT EXISTS `form_list` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `form_name` varchar(255) NOT NULL DEFAULT '',
  `form_title` varchar(255) NOT NULL DEFAULT '',
  `publish_type` varchar(10) NOT NULL DEFAULT '',
  `publish_groups` varchar(1024) NOT NULL DEFAULT '',
  `publish_deps` VARCHAR(4) NOT NULL DEFAULT '',
  `publish_status` varchar(5) NOT NULL DEFAULT '',
  `amount_form_submission` VARCHAR(6) NOT NULL DEFAULT '-1',
  `admin_users` varchar(255) NOT NULL DEFAULT '1',
  `form_note` text,
  `form_genral_style` longtext,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `form_tables`
--

DROP TABLE IF EXISTS `form_tables`;
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

DROP TABLE IF EXISTS `organization_tree`;
CREATE TABLE IF NOT EXISTS `organization_tree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `parent_id` varchar(64) NOT NULL DEFAULT '1',
  `dep_mngr_user_id` varchar(6) NOT NULL DEFAULT '',
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `organization_tree`
--

INSERT INTO `organization_tree` (`id`, `name`, `parent_id`, `dep_mngr_user_id`, `note`) VALUES
(1, 'org manager', '0', '1', NULL);
-- --------------------------------------------------------

--
-- Table structure for table `publish_type`
--
DROP TABLE IF EXISTS `publish_type`;
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

DROP TABLE IF EXISTS `registration_request`;
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

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `setting_group` varchar(10) NOT NULL DEFAULT '',
  `setting_name` varchar(128) NOT NULL DEFAULT '',
  `setting_nik` varchar(128) NOT NULL DEFAULT '',
  `setting_value` varchar(255) DEFAULT '',
  `options` text,
  `note` text,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`indx`, `setting_group`, `setting_name`, `setting_nik`, `setting_value`, `options`, `note`) VALUES
(1, 'general', 'appMode', 'Application Mode', '1', '{\"type\":\"select\", \"values\":[{\"text\":\"Debag\",\"value\":\"0\"},{\"text\":\"Production\",\"value\":\"1\"}] }', 'Application Mode:\r\n0- Debag,\r\n1- Production '),
(2, 'general', 'maxFileSeize', 'Max file seize', '1048576', '{\"type\":\"text\"}', 'default max file size allowed (Bytes). 1KB = 1024 Bytes. 1MB = 1048576 Bytes'),
(3, 'email', 'SMTP_host', 'SMTP host', 'localhost', '{\"type\":\"text\"}', 'Sets the SMTP hosts of your Email hosting'),
(4, 'email', 'SMTP_port', 'SMTP port', '25', '{\"type\":\"number\",\"min\":\"0\"}', 'ets the default SMTP server port'),
(5, 'email', 'SMTP_Auth', 'SMTP Auth', '0', '{\"type\":\"select\", \"values\":[{\"text\":\"true\",\"value\":\"1\"},{\"text\":\"false\",\"value\":\"0\"}] }', 'Sets SMTP authentication. Utilizes the Username and Password variables'),
(6, 'email', 'SMTP_Username', 'SMTP Username', '', '{\"type\":\"text\"}', 'Sets SMTP username'),
(7, 'email', 'SMTP_Password', 'SMTP Password', '', '{\"type\":\"password\"}', 'Sets SMTP password'),
(8, 'email', 'SMTP_Secure', 'SMTP Secure', '', '{\"type\":\"text\"}', 'Sets connection prefix. Options are \"\", \"ssl\" or \"tls\"'),
(9, 'general', 'enableUserRegistration', 'Enable User registration', '1', '{\"type\":\"select\",\"values\":[{\"text\":\"Yes\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]}', 'Enable User registration'),
(10, 'general', 'enableAutoRegistration', 'Auto registration', '1', '{\"type\":\"select\",\"values\":[{\"text\":\"Yes\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]}', 'Accept automatically user registration after verification'),
(11, 'general', 'newUserDefaultGroupId', 'New User Default Group', '3', '{\"type\":\"sqlselect\",\"table\":\"users_gropes\",\"term\":\"group_status=\'1\'\",\"column_text\":\"group_name\",\"column_value\":\"indx\"}', 'Default Group of new user'),
(12, 'general', 'enableUserPasswordRecovery', 'Enable User Password Recovery', '1', '{\"type\":\"select\",\"values\":[{\"text\":\"Yes\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]}', 'Enable User Password Recovery\r\n(\"Forgot password\")'),
(13, 'general', 'setAdministratorUsersAsDefaultFormManager', 'set Administrator Users As Default Form Manager', '1', '{\"type\":\"select\",\"values\":[{\"text\":\"Yes\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]}', 'set Administrator Users As Default Form Manager if form managers not set in form.'),
(15, 'general', 'enableUsingCookies', 'Enable Cookies', '1', '{\"type\":\"select\",\"values\":[{\"text\":\"Yes\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]}', 'Allow cookies to be used to avoid filling out a form more than once'),
(16, 'general', 'cookiesLifeTime', 'Cookies Lifetime', '365', '{\"type\":\"text\"}', 'Cookies lifetime (in days).'),
(17, 'email', 'from_email', 'From Email', 'info@localhost.com', '{\"type\":\"text\"}', 'Sets the From email address for the message'),
(18, 'email', 'from_name', 'From Name', 'localhost', '{\"type\":\"text\"}', 'Sets the From name of the message'),
(19, 'email', 'verification_mail_subject', 'Verification Mail Subject', 'Email Verification', '{\"type\":\"text\"}', 'Sets the Subject of the message'),
(20, 'email', 'reset_pass_mail_subject', 'Reset password mail subject', 'Reset password', '{\"type\":\"text\"}', 'Sets the Subject of the Reset password mail message'),
(21, 'general', 'enableFormManagersToEditFormTamplate', 'Enable Form-Managers Edit Form Tamplate', '0', '{\"type\":\"select\",\"values\":[{\"text\":\"Yes\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]}', 'Enable Form-Managers Edit Form Tamplate'),
(22, 'form_style', 'form_body_bgcolor_1', 'Form body bgcolor 1', 'rgba(44, 43, 239, 0.55)', '{\"type\":\"color\"}', 'Sets form body bgcolor 1'),
(23, 'form_style', 'form_body_bgcolor_2', 'Form body bgcolor 2', 'rgb(29, 59, 238)', '{\"type\":\"color\"}', 'Sets form body bgcolor 2'),
(24, 'form_style', 'form_body_bgcoloe_angle', 'Form body bgcolors angle', '0', '{\"type\":\"number\",\"min\":\"0\",\"max\":\"360\",\"step\":\"1\"}', 'Linear gradient color angle'),
(25, 'form_style', 'max_body_bgImg_size', 'Max Form body bgImage size', '1048576', '{\"type\":\"text\"}', 'Max Form body bgImage size allowed (Bytes). 1KB = 1024 Bytes. 1MB = 1048576 Bytes'),
(26, 'form_style', 'form_body_bgImage_attach', 'Form body bgImage attachment', 'scroll', '{\"type\":\"select\", \"values\":[{\"text\":\"scroll\",\"value\":\"scroll\"},{\"text\":\"fixed\",\"value\":\"fixed\"}]}', 'Form body bgImage attachment'),
(27, 'form_style', 'form_body_bgImage_position', 'Form body bgImage position', 'center center', '{\"type\":\"select\", \"values\":[{\"text\":\"left top\",\"value\":\"left top\"},{\"text\":\"left center\",\"value\":\"left center\"},{\"text\":\"left bottom\",\"value\":\"left bottom\"},{\"text\":\"right top\",\"value\":\"right top\"},{\"text\":\"right center\",\"value\":\"right center\"},{\"text\":\"right bottom\",\"value\":\"right bottom\"},{\"text\":\"center top\",\"value\":\"center top\"},{\"text\":\"center center\",\"value\":\"center center\"},{\"text\":\"center bottom\",\"value\":\"center bottom\"}]}', 'Form body bgImage position'),
(28, 'form_style', 'form_body_bgImage_repet', 'Form body bgImage repet', 'repeat', '{\"type\":\"select\", \"values\":[{\"text\":\"no-repeat\",\"value\":\"no-repeat\"},{\"text\":\"repeat\",\"value\":\"repeat\"},{\"text\":\"repeat-x\",\"value\":\"repeat-x\"},{\"text\":\"repeat-y\",\"value\":\"repeat-y\"}]}', 'Form body bgImage repet'),
(29, 'form_style', 'form_body_bgImage_size', 'Form body bgImage size', 'auto', '{\"type\":\"select\", \"values\":[{\"text\":\"Orginal size\",\"value\":\"auto\"},{\"text\":\"contain\",\"value\":\"contain\"},{\"text\":\"cover\",\"value\":\"cover\"}]}', 'Form body bgImage size'),
(30, 'form_style', 'form_width', 'Form width', '80', '{\"type\":\"number\",\"min\":\"0\",\"max\":\"100\",\"step\":\"1\"}', 'Form width (%)'),
(31, 'form_style', 'form_vertical_margin', 'Vertical margin', '5', '{\"type\":\"number\",\"min\":\"0\",\"max\":\"100\",\"step\":\"1\"}', 'Form Vertical margin (%)'),
(32, 'form_style', 'form_Background_color', 'Form bgcolor', 'rgba(255, 255, 255, 1)', '{\"type\":\"color\"}', 'Form Background Color'),
(33, 'form_style', 'form_opacity', 'Form Opacity', '100', '{\"type\":\"number\",\"min\":\"0\",\"max\":\"100\",\"step\":\"1\"}', 'Form Opacity (%)'),
(34, 'form_style', 'form_border_size', 'Form border size', '1', '{\"type\":\"number\",\"min\":\"0\",\"step\":\"1\"}', 'Form border size (px)'),
(35, 'form_style', 'form_border_type', 'Form border type', 'solid', '{\"type\":\"select\", \"values\":[{\"text\":\"solid\",\"value\":\"solid\"},{\"text\":\"dotted\",\"value\":\"dotted\"},{\"text\":\"dashed\",\"value\":\"dashed\"},{\"text\":\"double\",\"value\":\"double\"},{\"text\":\"groove\",\"value\":\"groove\"},{\"text\":\"ridge\",\"value\":\"ridge\"},{\"text\":\"inset\",\"value\":\"inset\"},{\"text\":\"outset\",\"value\":\"outset\"},{\"text\":\"none\",\"value\":\"none\"}]}', 'Form border type'),
(36, 'form_style', 'form_border_color', 'Form border color', 'rgba(0, 0, 0, 1)', '{\"type\":\"color\"}', 'Form border color'),
(37, 'form_style', 'form_border_radius', 'Form border radius', '5', '{\"type\":\"number\",\"min\":\"0\",\"step\":\"1\"}', 'Form border radius size (px)');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(250) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(200) NOT NULL DEFAULT '',
  `status` varchar(1) NOT NULL DEFAULT '0',
  `groups` varchar(255) NOT NULL DEFAULT '',
  `dep_id` VARCHAR(6) NOT NULL DEFAULT '',
  `forgot_verify` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `status`, `groups`, `forgot_verify`) VALUES
(1, 'admin', 'admin@localhost.com', '$2y$10$FTyD8hpjyNVCbxsdM2.M5eIEm1OQ0NqXj7Qyv60X38rR6Nxh/BoRy', '1', '1', '');


-- --------------------------------------------------------

--
-- Table structure for table `users_gropes`
--

DROP TABLE IF EXISTS `users_gropes`;
CREATE TABLE IF NOT EXISTS `users_gropes` (
  `indx` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL DEFAULT '',
  `group_status` varchar(1) NOT NULL DEFAULT '1',
  `admin_ids` varchar(255) NOT NULL DEFAULT '1',
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users_gropes`
--
INSERT INTO `users_gropes` (`indx`, `group_name`, `group_status`, `admin_ids`) VALUES
(1, 'administrator', '1', '1'),
(2, 'managers', '1', '1'),
(3, 'registered', '1', '1');

