--
-- Table structure for table `access`
--

CREATE TABLE IF NOT EXISTS `access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` char(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `users_id` int(11) NOT NULL,
  `access_groups_id` int(11) NOT NULL,
  `chmod` char(3) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module` (`module`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `access_groups`
--

CREATE TABLE IF NOT EXISTS `access_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `access_groups`
--

INSERT INTO `access_groups` (`id`, `name`) VALUES
(1, 'root'),
(2, 'banned');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_groups_id` int(11) DEFAULT NULL,
  `nickname` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nickname` (`nickname`),
  UNIQUE KEY `email` (`email`),
  KEY `access_groups_id` (`access_groups_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `access_groups_id`, `nickname`, `email`, `password`) VALUES
(1, 1, 'root', 'nobody@dev.null', '1c4a9f6d28fadf7594d5c83dc4137c12');

-- --------------------------------------------------------

--
-- Table structure for table `users_info`
--

CREATE TABLE IF NOT EXISTS `users_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `given_name` varchar(255) DEFAULT NULL,
  `family_name` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `locality` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `country` char(2) DEFAULT NULL,
  `tel` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `language` char(2) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_id` (`users_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users_info`
--

INSERT INTO `users_info` (`id`, `users_id`, `given_name`, `family_name`, `street_address`, `locality`, `region`, `postcode`, `country`, `tel`, `url`, `dob`, `gender`, `language`, `timezone`, `image`) VALUES
(1, 1, '', '', '', '', '', '', '', '', '', '0000-00-00', NULL, '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_marketing`
--

CREATE TABLE IF NOT EXISTS `users_marketing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `signup_date` date NOT NULL,
  `referrer` text,
  `referral_id` text,
  `landing_page` text,
  `search_keywords` text,
  `how_they_heard_about_us` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
