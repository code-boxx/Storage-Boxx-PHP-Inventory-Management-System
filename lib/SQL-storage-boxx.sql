CREATE TABLE `options` (
  `option_name` varchar(255) NOT NULL,
  `option_description` varchar(255) DEFAULT NULL,
  `option_value` varchar(255) NOT NULL,
  `option_group` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `stock` (
  `stock_sku` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `stock_name` varchar(255) NOT NULL,
  `stock_desc` varchar(255) DEFAULT NULL,
  `stock_unit` varchar(255) NOT NULL,
  `stock_qty` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `stock_mvt` (
  `stock_sku` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `mvt_date` datetime NOT NULL,
  `mvt_direction` varchar(1) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `mvt_qty` decimal(12,2) NOT NULL,
  `mvt_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_profilepic` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `stock`
  ADD PRIMARY KEY (`stock_sku`);

ALTER TABLE `stock_mvt`
  ADD PRIMARY KEY (`stock_sku`,`mvt_date`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `mvt_direction` (`mvt_direction`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD KEY `user_name` (`user_name`);

ALTER TABLE `users`
  MODIFY `user_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

INSERT INTO `options` (`option_name`, `option_description`, `option_value`, `option_group`) VALUES
  ('EMAIL_FROM', 'System email from.', 'sys@site.com', 1),
  ('PAGE_PER', 'Number of entries per page.', '20', 1);
