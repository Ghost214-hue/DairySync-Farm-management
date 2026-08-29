-- =====================================================================
-- Migration 002: Dedicated collections table
-- Customer payments were previously stored in `income` with
-- source = 'Collection', which caused double-counting in revenue
-- reports (milk sale + its payment both counted as income).
--
-- This migration:
--   1. Creates the `collections` table
--   2. Migrates existing payment rows out of `income` (data preserved:
--      user, farm, customer, amount, payment date, created timestamp)
--   3. Removes the Collection rows from `income`
-- Safe to run once; re-running after step 3 is a no-op.
-- =====================================================================

CREATE TABLE IF NOT EXISTS collections (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    farm_id INT(11) NOT NULL,
    customer_id INT(11) NOT NULL,
    customer_name VARCHAR(100) DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'Other',
    reference_number VARCHAR(100) DEFAULT NULL,
    payment_date DATE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    INDEX idx_collections_user (user_id),
    INDEX idx_collections_customer (customer_id),
    INDEX idx_collections_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO collections (user_id, farm_id, customer_id, customer_name, amount, payment_method, payment_date, created_at)
SELECT user_id, farm_id, IFNULL(customer_id, 0), customer_name, total_amount, 'Other', income_date, created_at
FROM income
WHERE source = 'Collection'
  AND NOT EXISTS (
      SELECT 1 FROM collections c
      WHERE c.user_id = income.user_id
        AND c.customer_id = IFNULL(income.customer_id, 0)
        AND c.amount = income.total_amount
        AND c.payment_date = income.income_date
  );

DELETE FROM income WHERE source = 'Collection';