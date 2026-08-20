-- ARNQuickFix feature migration
-- Run this against arn_quickfix AFTER taking a backup.
-- The PHP migration runner included in this project is safer for backfill operations.

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS assets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    asset_id VARCHAR(32) NOT NULL,
    customer_id BIGINT UNSIGNED NULL,
    customer_email VARCHAR(255) NOT NULL,
    asset_type VARCHAR(32) NOT NULL,
    asset_brand VARCHAR(150) NULL,
    asset_details TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_assets_asset_id (asset_id),
    KEY idx_assets_customer_email (customer_email),
    KEY idx_assets_customer_id (customer_id),
    KEY idx_assets_type (asset_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS asset_sequences (
    prefix VARCHAR(8) NOT NULL,
    next_number BIGINT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (prefix)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO asset_sequences(prefix, next_number) VALUES ('AC', 0), ('ELV', 0), ('GEN', 0)
ON DUPLICATE KEY UPDATE prefix = VALUES(prefix);

CREATE TABLE IF NOT EXISTS technician_reviews (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    service_request_id BIGINT UNSIGNED NOT NULL,
    asset_id BIGINT UNSIGNED NULL,
    customer_id BIGINT UNSIGNED NULL,
    customer_email VARCHAR(255) NOT NULL,
    technician_id BIGINT UNSIGNED NULL,
    rating TINYINT UNSIGNED NOT NULL,
    review TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_review_service_request (service_request_id),
    KEY idx_reviews_technician (technician_id),
    KEY idx_reviews_customer (customer_id),
    KEY idx_reviews_asset (asset_id),
    CONSTRAINT chk_review_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_price_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    service_request_id BIGINT UNSIGNED NOT NULL,
    asset_id BIGINT UNSIGNED NULL,
    previous_price DECIMAL(12,2) NULL,
    new_price DECIMAL(12,2) NOT NULL,
    changed_by BIGINT UNSIGNED NULL,
    changed_by_email VARCHAR(255) NULL,
    reason VARCHAR(500) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_price_history_request (service_request_id),
    KEY idx_price_history_asset (asset_id),
    KEY idx_price_history_changed_by (changed_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS technician_promotions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    technician_id BIGINT UNSIGNED NOT NULL,
    previous_position VARCHAR(150) NULL,
    new_position VARCHAR(150) NOT NULL,
    promotion_date DATE NOT NULL,
    reason VARCHAR(1000) NULL,
    approved_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_promotions_technician (technician_id),
    KEY idx_promotions_date (promotion_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- These columns are added by the PHP migration runner only when missing because
-- MySQL versions differ in their support for ADD COLUMN IF NOT EXISTS.
-- service_requests: asset_ref_id, technician_id, original_price, current_price
-- users: position

SET FOREIGN_KEY_CHECKS = 1;
