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
('APP_VER', 'App version', 1, 0),
('EMAIL_FROM', 'System email from', 'sys@site.com', 1),
('PAGE_PER', 'Number of entries per page', 20, 1),
('SUGGEST_LIMIT', 'Autocomplete suggestion limit', 5, 1),
('D_LONG', 'MYSQL date format (long)', '%e %M %Y', 1),
('D_SHORT', 'MYSQL date format (short)', '%Y-%m-%d', 1),
('DT_LONG', 'MYSQL date time format (long)', '%e %M %Y %l:%i:%S %p', 1),
('DT_SHORT', 'MYSQL date time format (short)', '%Y-%m-%d %H:%i:%S', 1),
('STOCK_MVT', 'Stock movement code', '{\"I\":\"Stock In (Receive)\",\"O\":\"Stock Out (Dispatch)\",\"T\":\"Stock Take (Audit)\",\"D\":\"Stock Discard (Dispose)\"}', 2);

-- (B) USERS
CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL,
  `user_level` varchar(1) NOT NULL DEFAULT 'U',
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD KEY `user_name` (`user_name`),
  ADD KEY `user_level` (`user_level`);

ALTER TABLE `users`
  MODIFY `user_id` bigint(20) NOT NULL AUTO_INCREMENT;

-- (C) USERS HASH
CREATE TABLE `users_hash` (
  `user_id` bigint(20) NOT NULL,
  `hash_for` varchar(3) NOT NULL,
  `hash_code` text NOT NULL,
  `hash_time` datetime NOT NULL,
  `hash_tries` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `users_hash`
  ADD PRIMARY KEY (`user_id`, `hash_for`);

-- (D) PUSH NOTIFICATIONS
CREATE TABLE `webpush` (
  `endpoint` varchar(255) NOT NULL,
  `user_id` bigint(20) NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `webpush`
  ADD PRIMARY KEY (`endpoint`),
  ADD KEY `user_id` (`user_id`);

-- (E) ITEMS
CREATE TABLE `items` (
  `item_sku` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_desc` varchar(255) DEFAULT NULL,
  `item_unit` varchar(255) NOT NULL,
  `item_low` decimal(12,2) NOT NULL DEFAULT 0.00,
  `item_qty` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `items`
  ADD PRIMARY KEY (`item_sku`),
  ADD KEY `item_low` (`item_low`),
  ADD KEY `item_qty` (`item_qty`);

-- (F) ITEM BATCHES
CREATE TABLE `item_batches` (
  `item_sku` varchar(255) NOT NULL,
  `batch_name` varchar(255) NOT NULL,
  `batch_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `batch_qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `batch_expire` date NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `item_batches`
  ADD PRIMARY KEY (`item_sku`,`batch_name`),
  ADD KEY `batch_qty` (`batch_qty`),
  ADD KEY `batch_expire` (`batch_expire`);

-- (G) ITEM MOVEMENT
CREATE TABLE `item_mvt` (
  `item_sku` varchar(255) NOT NULL,
  `batch_name` varchar(255) NOT NULL,
  `mvt_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mvt_direction` varchar(1) NOT NULL,
  `mvt_qty` decimal(12,2) NOT NULL,
  `mvt_notes` text DEFAULT NULL,
  `item_left` decimal(12,2) NOT NULL,
  `batch_left` decimal(12,2) NOT NULL,
  `user_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `item_mvt`
  ADD PRIMARY KEY (`item_sku`,`batch_name`,`mvt_date`),
  ADD KEY `mvt_direction` (`mvt_direction`),
  ADD KEY `user_name` (`user_name`);

-- (H) SUPPLIERS
CREATE TABLE `suppliers` (
  `sup_id` bigint(20) NOT NULL,
  `sup_name` varchar(255) NOT NULL,
  `sup_tel` varchar(32) NOT NULL,
  `sup_email` varchar(255) NOT NULL,
  `sup_address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`sup_id`),
  ADD UNIQUE KEY `sup_email` (`sup_email`),
  ADD KEY `sup_name` (`sup_name`);

ALTER TABLE `suppliers`
  MODIFY `sup_id` bigint(20) NOT NULL AUTO_INCREMENT;

-- (I) SUPPLIER ITEMS
CREATE TABLE `suppliers_items` (
  `sup_id` bigint(20) NOT NULL,
  `item_sku` varchar(255) NOT NULL,
  `sup_sku` varchar(255) DEFAULT NULL,
  `unit_price` decimal(12,2) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `suppliers_items`
  ADD PRIMARY KEY (`sup_id`,`item_sku`),
  ADD KEY `sup_sku` (`sup_sku`);