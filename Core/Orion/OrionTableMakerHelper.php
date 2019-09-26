-- TODO: Migrations class for Orion

CREATE TABLE `users` (
`id` bigint(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
`first_name` varchar(255) NOT NULL,
`last_name` varchar(255) NOT NULL,
`email` varchar(255) NOT NULL UNIQUE KEY,
`password_hash` varchar(255) NOT NULL,
`password_reset_hash` varchar(255) DEFAULT NULL,
`password_reset_expires_at` datetime DEFAULT NULL,
`activation_hash` varchar(255) DEFAULT NULL,
`remember_token_hash` varchar(255) DEFAULT NULL,
`remember_token_expires_at` datetime DEFAULT NULL,
`is_active` tinyint(1) NOT NULL,
`is_disabled` tinyint(1) NOT NULL,
`status` varchar(255) NOT NULL,
`image` varchar(255) DEFAULT NULL,
`api_key` varchar(255) DEFAULT NULL UNIQUE KEY,
`phone_number` varchar(64) NOT NULL,
`gender` varchar(10) NOT NULL,
`date_of_birth` datetime NOT NULL,
`street` varchar(255) NOT NULL,
`house_or_apartment` varchar(255) NOT NULL,
`city` varchar(255) NOT NULL,
`state_or_province` varchar(255) NOT NULL,
`post_code` varchar(64) NOT NULL,
`country` varchar(64) NOT NULL,
`created_at` timestamp,
`updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `remembered_logins` (
`id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
`token_hash` varchar(64) NOT NULL,
`user_id` bigint(20) NOT NULL UNIQUE KEY,
`expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `failed_logins` (
`id` bigint(20) NOT NULL PRIMARY AUTO_INCREMENT,
`user_id` bigint(20) NOT NULL,
`email` varchar(255) NOT NULL,
`failed_login_count` int(2) NOT NULL,
`failed_login_last` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `page_contents` (
`id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
`page_uri` varchar(255) NOT NULL,
`theme` varchar(255) NOT NULL,
`title` varchar(255) NOT NULL,
`user_id` bigint(20) NOT NULL,
`name` varchar(255) NOT NULL,
`email` varchar(255) NOT NULL,
`content` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `site_visitors` (
`id` int(255) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`ip` varchar(50) DEFAULT NULL,
`browser` varchar(255) NOT NULL,
`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
`hour` smallint(2) NOT NULL,
`minute` smallint(2) NOT NULL,
`day` smallint(2) NOT NULL,
`month` smallint(2) NOT NULL,
`year` smallint(4) NOT NULL,
`referrer` varchar(255) DEFAULT NULL,
`page` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;