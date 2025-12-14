-- Membership Freeze Feature
-- Add freeze functionality to subscriptions

-- Add freeze fields to subscriptions table
ALTER TABLE `subscriptions` 
ADD COLUMN `freeze_start_date` DATE NULL,
ADD COLUMN `freeze_end_date` DATE NULL,
ADD COLUMN `freeze_reason` TEXT NULL,
ADD COLUMN `is_frozen` BOOLEAN DEFAULT FALSE;

-- Create freeze history table for tracking all freeze requests
CREATE TABLE `membership_freeze_history` (
  `freeze_id` INT PRIMARY KEY AUTO_INCREMENT,
  `subscription_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `freeze_start` DATE NOT NULL,
  `freeze_end` DATE NOT NULL,
  `reason` TEXT,
  `requested_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `approved_by` INT NULL,
  `approved_at` TIMESTAMP NULL,
  `status` ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
  `admin_notes` TEXT NULL,
  FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions`(`subscription_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `members`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`approved_by`) REFERENCES `members`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add indexes for performance
CREATE INDEX idx_freeze_status ON membership_freeze_history(status);
CREATE INDEX idx_freeze_user ON membership_freeze_history(user_id);
CREATE INDEX idx_freeze_dates ON membership_freeze_history(freeze_start, freeze_end);
