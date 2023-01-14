-- (A) SUPPLIERS
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

-- (B) SUPPLIER ITEMS
CREATE TABLE `suppliers_items` (
  `sup_id` bigint(20) NOT NULL,
  `stock_sku` varchar(255) NOT NULL,
  `sup_sku` varchar(255) DEFAULT NULL,
  `unit_price` decimal(12,2) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `suppliers_items`
  ADD PRIMARY KEY (`sup_id`,`stock_sku`),
  ADD KEY `sup_sku` (`sup_sku`);