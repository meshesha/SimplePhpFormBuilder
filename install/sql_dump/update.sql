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
  `publish_status` varchar(5) NOT NULL DEFAULT '',
  `admin_users` varchar(255) NOT NULL DEFAULT '1',
  `form_note` text,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
ALTER TABLE `form_list` ADD COLUMN `form_genral_style` longtext;

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
  `setting_value` varchar(255) NOT NULL DEFAULT '',
  `options` varchar(255) NOT NULL DEFAULT '',
  `note` text,
  PRIMARY KEY (`indx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`indx`, `setting_group`, `setting_name`, `setting_nik`, `setting_value`, `options`, `note`) VALUES
(1, 'general', 'appMode', 'Application Mode', '0', '{\"type\":\"select\", \"values\":[{\"text\":\"Debag\",\"value\":\"0\"},{\"text\":\"Production\",\"value\":\"1\"}] }', 'Application Mode:\r\n0- Debag,\r\n1- Production '),
(2, 'general', 'maxFileSeize', 'Max file seize', '1048576', '{\"type\":\"text\"}', 'default max file size allowed (Bytes). 1KB = 1024 Bytes. 1MB = 1048576 Bytes'),
(3, 'email', 'SMTP_host', 'SMTP host', 'localhost', '{\"type\":\"text\"}', 'Sets the SMTP hosts of your Email hosting'),
(4, 'email', 'SMTP_port', 'SMTP port', '25', '{\"type\":\"number\"}', 'ets the default SMTP server port'),
(5, 'email', 'SMTP_Auth', 'SMTP Auth', '0', '{\"type\":\"select\", \"values\":[{\"text\":\"true\",\"value\":\"1\"},{\"text\":\"false\",\"value\":\"0\"}] }', 'Sets SMTP authentication. Utilizes the Username and Password variables'),
(6, 'email', 'SMTP_Username', 'SMTP Username', '', '{\"type\":\"text\"}', 'Sets SMTP username'),
(7, 'email', 'SMTP_Password', 'SMTP Password', '', '{\"type\":\"password\"}', 'Sets SMTP password'),
(8, 'email', 'SMTP_Secure', 'SMTP Secure', '', '{\"type\":\"text\"}', 'Sets connection prefix. Options are \"\", \"ssl\" or \"tls\"'),
(9, 'general', 'enableUserRegistration', 'Enable User registration', '1', '{\"type\":\"select\",\"values\":[{\"text\":\"Yes\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]}', 'Enable User registration'),
(10, 'general', 'enableAutoRegistration', 'Auto registration', '0', '{\"type\":\"select\",\"values\":[{\"text\":\"Yes\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]}', 'Accept automatically user registration after verification'),
(11, 'general', 'newUserDefaultGroupId', 'New User Default Group', '2', '{\"type\":\"sqlselect\",\"table\":\"users_gropes\",\"term\":\"group_status=\'1\'\",\"column_text\":\"group_name\",\"column_value\":\"indx\"}', 'Default Group of new user'),
(12, 'general', 'enableUserPasswordRecovery', 'Enable User Password Recovery', '1', '{\"type\":\"select\",\"values\":[{\"text\":\"Yes\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]}', 'Enable User Password Recovery\r\n(\"Forgot password\")'),
(13, 'general', 'setAdministratorUsersAsDefaultFormManager', 'set Administrator Users As Default Form Manager', '1', '{\"type\":\"select\",\"values\":[{\"text\":\"Yes\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]}', 'set Administrator Users As Default Form Manager if form managers not set in form.'),
(15, 'general', 'enableUsingCookies', 'Enable Cookies', '0', '{\"type\":\"select\",\"values\":[{\"text\":\"Yes\",\"value\":\"1\"},{\"text\":\"No\",\"value\":\"0\"}]}', 'Allow cookies to be used to avoid filling out a form more than once'),
(16, 'general', 'cookiesLifeTime', 'Cookies Lifetime', '30', '{\"type\":\"text\"}', 'Cookies lifetime (in days).'),
(17, 'email', 'from_email', 'From Email', 'info@localhost.com', '{\"type\":\"text\"}', 'Sets the From email address for the message'),
(18, 'email', 'from_name', 'From Name', 'localhost', '{\"type\":\"text\"}', 'Sets the From name of the message'),
(19, 'email', 'verification_mail_subject', 'Verification Mail Subject', 'Email Verification', '{\"type\":\"text\"}', 'Sets the Subject of the message'),
(20, 'email', 'reset_pass_mail_subject', 'Reset password mail subject', 'Reset password', '{\"type\":\"text\"}', 'Sets the Subject of the Reset password mail message');

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
  `forgot_verify` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users_gropes`
--

INSERT INTO `users_gropes` (`indx`, `group_name`, `group_status`, `admin_ids`) VALUES
(1, 'administrator', '1', '1'),
(2, 'managers', '1', '1');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
