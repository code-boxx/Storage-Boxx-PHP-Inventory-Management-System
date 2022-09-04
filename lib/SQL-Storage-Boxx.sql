-- (A) SETTINGS
CREATE TABLE `settings` (
  `setting_name` varchar(255) NOT NULL,
  `setting_description` varchar(255) DEFAULT NULL,
  `setting_value` varchar(255) NOT NULL,
  `setting_group` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_name`),
  ADD KEY `setting_group` (`setting_group`);

INSERT INTO `settings` (`setting_name`, `setting_description`, `setting_value`, `setting_group`) VALUES
('APP_VER', 'App version', '1', 0),
('EMAIL_FROM', 'System email from', 'sys@site.com', 1),
('PAGE_PER', 'Number of entries per page', '20', 1),
('STOCK_MVT', 'Stock movement code', '{\"I\":\"Stock In (Receive)\",\"O\":\"Stock Out (Dispatch)\",\"T\":\"Stock Take (Audit)\",\"D\":\"Stock Discard (Dispose)\"}', 0);

-- (B) USERS
CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD KEY `user_name` (`user_name`);

ALTER TABLE `users`
  MODIFY `user_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- (C) STOCK
CREATE TABLE `stock` (
  `stock_sku` varchar(255) NOT NULL,
  `stock_name` varchar(255) NOT NULL,
  `stock_desc` varchar(255) DEFAULT NULL,
  `stock_unit` varchar(255) NOT NULL,
  `stock_low` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock_qty` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `stock`
  ADD PRIMARY KEY (`stock_sku`),
  ADD KEY `stock_low` (`stock_low`),
  ADD KEY `stock_qty` (`stock_qty`);

-- (D) STOCK MOVEMENT
CREATE TABLE `stock_mvt` (
  `stock_sku` varchar(255) NOT NULL,
  `mvt_date` datetime NOT NULL,
  `mvt_direction` varchar(1) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `mvt_qty` decimal(12,2) NOT NULL,
  `mvt_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `stock_mvt`
  ADD PRIMARY KEY (`stock_sku`,`mvt_date`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `mvt_direction` (`mvt_direction`);
