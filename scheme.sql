SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `nh_payouts` (
  `uid` int(11) NOT NULL,
  `rig_uid` int(11) NOT NULL,
  `btc_amount` double NOT NULL,
  `rate` double NOT NULL,
  `currency_amount` double NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `nh_payouts_backup` (
  `uid` int(11) NOT NULL,
  `rig_uid` int(11) NOT NULL,
  `btc_amount` double NOT NULL,
  `rate` double NOT NULL,
  `currency_amount` double NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `payouts` (
  `uid` int(11) NOT NULL,
  `user_uid` int(11) NOT NULL,
  `amount` double NOT NULL,
  `address` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `txid` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wallet_uid` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `rigs` (
  `uid` int(11) NOT NULL,
  `rig_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `worker_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `hardware` text COLLATE utf8_unicode_ci,
  `unpaid` double NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `stats` (
  `uid` int(11) NOT NULL,
  `rig_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `worker_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `unpaid` double NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `address` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `worker_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `mined` double NOT NULL DEFAULT '0',
  `payed` double NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `variables` (
  `uid` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `nh_payouts`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `rig_uid` (`rig_uid`,`timestamp`);

ALTER TABLE `nh_payouts_backup`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `rig_uid` (`rig_uid`,`timestamp`);

ALTER TABLE `payouts`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `user_uid` (`user_uid`,`timestamp`);

ALTER TABLE `rigs`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `worker_id` (`rig_id`,`worker_id`) USING BTREE,
  ADD KEY `worker_id_2` (`worker_id`);

ALTER TABLE `stats`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `worker_id` (`worker_id`),
  ADD KEY `rig_id` (`rig_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `address` (`worker_id`) USING BTREE;

ALTER TABLE `variables`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `name` (`name`);


ALTER TABLE `nh_payouts`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `nh_payouts_backup`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `payouts`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `rigs`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `stats`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `variables`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;
