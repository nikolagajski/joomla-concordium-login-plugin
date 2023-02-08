CREATE TABLE IF NOT EXISTS `#__concordium_nonce`
(
    `user_id`         int(11)      NULL DEFAULT NULL,
    `nonce`           varchar(255) NOT NULL,
    `account_address` varchar(255) NOT NULL,
    `created_at`      datetime     NOT NULL,
    UNIQUE KEY `idx_account_address` (`account_address`),
    UNIQUE KEY `idx_user_id` (`user_id`) USING BTREE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;

ALTER TABLE `#__concordium_nonce`
    ADD FOREIGN KEY (`user_id`) REFERENCES `#__users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;
