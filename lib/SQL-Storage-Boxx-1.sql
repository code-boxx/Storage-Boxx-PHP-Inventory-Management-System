-- (A) FORGOTTEN PASSWORD RESET
CREATE TABLE `password_reset` (
  `user_id` bigint(20) NOT NULL,
  `reset_hash` varchar(64) NOT NULL,
  `reset_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`user_id`);

-- (B) NFC LOGIN TOKEN
ALTER TABLE `users` ADD `user_token` VARCHAR(32) NULL DEFAULT NULL AFTER `user_password`;

-- (C) STOCK LEFT
ALTER TABLE `stock_mvt` ADD `mvt_left` DECIMAL(12,2) NOT NULL DEFAULT '0' AFTER `mvt_qty`;