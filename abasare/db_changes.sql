-- 2024-06-19
ALTER TABLE `users` CHANGE `password` `password` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;  

-- 2024-06-20
ALTER TABLE `interest` ADD `member_id` INT NULL AFTER `id`, ADD INDEX (`member_id`); 
ALTER TABLE `interest` ADD CONSTRAINT `interest_foreign_key_3` FOREIGN KEY (`member_id`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 
ALTER TABLE `interest` ADD `amount` DOUBLE NULL AFTER `member_id`; 
UPDATE member SET birth_date=NULL WHERE birth_date=''; 
ALTER TABLE `member` CHANGE `birth_date` `birth_date` DATE NULL DEFAULT NULL; 

-- Re-order members columns
ALTER TABLE `member` CHANGE `fname` `fname` VARCHAR(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `id`, CHANGE `lname` `lname` VARCHAR(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `fname`, CHANGE `phone_cell` `phone_cell` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `lname`, CHANGE `email` `email` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `phone_cell`, CHANGE `birth_date` `birth_date` DATE NULL DEFAULT NULL AFTER `email`, CHANGE `civil_status` `civil_status` VARCHAR(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `birth_date`, CHANGE `sex` `sex` VARCHAR(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `civil_status`, CHANGE `address` `address` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `sex`, CHANGE `employment_status` `employment_status` VARCHAR(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `address`;

-- 2024-06-21
ALTER TABLE `users` CHANGE `photo` `photo` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL; 
ALTER TABLE `users` CHANGE `about` `about` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL; 
ALTER TABLE `users` CHANGE `staff_id` `staff_id` INT NULL, CHANGE `member_acc` `member_acc` INT NULL; 

ALTER TABLE `users` ADD INDEX(`staff_id`);
ALTER TABLE `users` ADD INDEX(`member_acc`);

UPDATE `users` SET `staff_id` = NULL WHERE `users`.`id` = 69; 
UPDATE `users` SET `member_acc` = NULL WHERE `member_acc` NOT IN (SELECT id FROM member);
ALTER TABLE `users` ADD CONSTRAINT `users_foreign_key_1` FOREIGN KEY (`staff_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 
ALTER TABLE `users` ADD CONSTRAINT `users_foreign_key_2` FOREIGN KEY (`member_acc`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

ALTER TABLE `interest` CHANGE `loan_interest` `loan_interest` FLOAT NULL; 
ALTER TABLE `interest` CHANGE `membership_fee` `membership_fee` INT NULL; 
ALTER TABLE `interest` CHANGE `saving_overdu` `saving_overdu` INT NULL; 
ALTER TABLE `interest` CHANGE `desciption` `desciption` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL; 
ALTER TABLE `member` CHANGE `mi` `mi` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL; 

--2024-06-22

-- Make sure to remove all records for profile picture as non of them is valid in any server.
UPDATE users SET photo=NULL WHERE photo='images/sam.jpg';
UPDATE `users` SET `photo` = NULL WHERE `users`.`id` = 16; 

-- share capita manupilation
ALTER TABLE `capital_share` CHANGE `member_id` `member_id` INT(11) NULL; 
ALTER TABLE `capital_share` ADD INDEX(`member_id`);
UPDATE capital_share SET member_id = NULL WHERE member_id NOT IN (SELECT id FROM member);
ALTER TABLE `capital_share` ADD CONSTRAINT `capital_share_foreign_key_1` FOREIGN KEY (`member_id`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 
ALTER TABLE `capital_share` ADD `staff_id` INT NULL AFTER `id`, ADD INDEX (`staff_id`); 
ALTER TABLE `capital_share` ADD CONSTRAINT `capital_share_foreign_key_2` FOREIGN KEY (`staff_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

-- Member loans manupilation system
UPDATE member_loans SET member_id=NULL WHERE member_id NOT IN (SELECT id FROM member);
ALTER TABLE `member_loans` ADD INDEX(`member_id`);
ALTER TABLE `member_loans` ADD CONSTRAINT `member_loans_foreign_key_1` FOREIGN KEY (`member_id`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

ALTER TABLE `member_loans` ADD INDEX(`loan_id`);
UPDATE member_loans SET loan_id=NULL WHERE loan_id NOT IN (SELECT id FROM loan_type);
ALTER TABLE `loan_type` ENGINE = INNODB;
ALTER TABLE `member_loans` ADD CONSTRAINT `member_loans_foreign_key_2` FOREIGN KEY (`loan_id`) REFERENCES `loan_type`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

ALTER TABLE `member_loans` ADD INDEX(`next_payment_id`);
ALTER TABLE `lend_payments` ADD UNIQUE(`payment_number`);
UPDATE member_loans SET next_payment_id = NULL WHERE next_payment_id NOT IN (SELECT payment_number FROM lend_payments);
ALTER TABLE `member_loans` ADD CONSTRAINT `member_loans_foreign_key_3` FOREIGN KEY (`next_payment_id`) REFERENCES `lend_payments`(`payment_number`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

ALTER TABLE `member_loans` ADD INDEX(`staff_portal`);
UPDATE member_loans SET staff_portal = NULL WHERE staff_portal NOT IN (SELECT id FROM users);

ALTER TABLE `member_loans` ADD CONSTRAINT `member_loans_foreign_key_4` FOREIGN KEY (`staff_portal`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

UPDATE member SET income=NULL WHERE income='';

-- 2024-06-23
CREATE TABLE `overdue_settings` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `month` INT NOT NULL , `year` INT NOT NULL , `saving_overdue` DATE NOT NULL , `payment_overdue` DATE NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB; 
ALTER TABLE `saving` ADD INDEX(`member_id`);
ALTER TABLE `saving` CHANGE `member_id` `member_id` INT(11) NULL; 
UPDATE saving SET member_id=NULL WHERE member_id NOT IN (SELECT id FROM member);
ALTER TABLE `saving` ADD CONSTRAINT `saving_foreign_key_1` FOREIGN KEY (`member_id`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 
ALTER TABLE `interest` ADD `saving_id` INT NULL AFTER `membership_fee`, ADD INDEX (`saving_id`); 
ALTER TABLE `interest` ADD CONSTRAINT `interest_foreign_key_4` FOREIGN KEY (`saving_id`) REFERENCES `saving`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

-- 2014-06-29 
ALTER TABLE `sacial_saving` CHANGE `m_id` `m_id` INT(11) NULL; 
ALTER TABLE `sacial_saving` ADD INDEX(`m_id`);
UPDATE sacial_saving SET m_id=NULL WHERE m_id NOT IN(SELECT id FROM member);
ALTER TABLE `sacial_saving` ADD CONSTRAINT `social_saving_foreign_key_1` FOREIGN KEY (`m_id`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

-- 2024-07-10
ALTER TABLE `users` ADD `signature` VARCHAR(255) NULL AFTER `photo`; 


-- 2024-07-14
ALTER TABLE `member_loans` ADD `signatory_1` INT NULL AFTER `staff_portal`, ADD `signatory_1_status` BOOLEAN NULL AFTER `signatory_1`, ADD `signatory_1_comment` VARCHAR(255) NULL AFTER `signatory_1_status`, ADD `signatory_2` INT NULL AFTER `signatory_1_comment`, ADD `signatory_2_status` BOOLEAN NULL AFTER `signatory_2`, ADD `signatory_2_comment` VARCHAR(255) NULL AFTER `signatory_2_status`, ADD INDEX (`signatory_2`), ADD INDEX (`signatory_1`); 

ALTER TABLE `member_loans` ADD CONSTRAINT `member_loans_foreign_key_5` FOREIGN KEY (`signatory_1`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `member_loans` ADD CONSTRAINT `member_loans_foreign_key_6` FOREIGN KEY (`signatory_2`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 



-- 2025-01-16
CREATE TABLE `provinces` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`), UNIQUE (`name`)) ENGINE = InnoDB; 

CREATE TABLE `districts` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `province_id` INT UNSIGNED NOT NULL , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`), INDEX (`province_id`)) ENGINE = InnoDB; 

ALTER TABLE `districts` ADD CONSTRAINT `districts_foreign_key_1` FOREIGN KEY (`province_id`) REFERENCES `provinces`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

CREATE TABLE `sectors` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `district_id` INT UNSIGNED NOT NULL , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`), INDEX (`district_id`)) ENGINE = InnoDB; 

ALTER TABLE `sectors` ADD CONSTRAINT `sectors_foreign_key_1` FOREIGN KEY (`district_id`) REFERENCES `districts`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

CREATE TABLE `cells` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `sector_id` INT UNSIGNED NOT NULL , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`), INDEX (`sector_id`)) ENGINE = InnoDB; 

ALTER TABLE `cells` ADD CONSTRAINT `cells_foreign_key_1` FOREIGN KEY (`sector_id`) REFERENCES `sectors`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

CREATE TABLE `villages` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `cell_id` INT UNSIGNED NOT NULL , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`), INDEX (`cell_id`)) ENGINE = InnoDB; 

ALTER TABLE `villages` ADD CONSTRAINT `villages_foreign_key_1` FOREIGN KEY (`cell_id`) REFERENCES `cells`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 


ALTER TABLE `member` ADD `id_number` VARCHAR(255) NULL AFTER `phone_cell`, ADD `id_location` VARCHAR(255) NULL AFTER `id_number`, ADD UNIQUE (`id_number`);

ALTER TABLE `member` ADD `village_id` INT UNSIGNED NULL AFTER `address`, ADD INDEX (`village_id`); 

ALTER TABLE `member` ADD CONSTRAINT `member_foreign_key_1` FOREIGN KEY (`village_id`) REFERENCES `villages`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 


-- 2025-01-19
ALTER TABLE `member_loans` CHANGE `president` `president` INT(11) NULL, CHANGE `accountant` `accountant` INT(11) NULL, CHANGE `reject` `reject` INT(11) NULL; 

ALTER TABLE `member_loans` ADD `rejected_by` INT NULL AFTER `reject`, ADD `reject_comment` VARCHAR(255) NULL AFTER `rejected_by`, ADD INDEX (`rejected_by`);

ALTER TABLE `member_loans` ADD CONSTRAINT `member_loans_foreign_key_7` FOREIGN KEY (`rejected_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 


-- 2025 01-21
ALTER TABLE `member_loans` CHANGE `accountant` `committee_status` INT(11) NULL DEFAULT NULL; 

ALTER TABLE `member_loans` ADD `committee` INT NULL AFTER `committee_status`, ADD `committee_date` DATE NULL AFTER `committee`, ADD `committee_reject_comment` VARCHAR(255) NULL AFTER `committee_date`, ADD INDEX (`committee`); 

ALTER TABLE `member_loans` ADD CONSTRAINT `member_loans_foreign_key_8` FOREIGN KEY (`committee`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

-- 2025-01-23
ALTER TABLE `member_loans` CHANGE `president` `president_status` INT NULL DEFAULT NULL; 

ALTER TABLE `member_loans` ADD `president` INT(11) NULL AFTER `president_status`, ADD `president_date` DATE NULL AFTER `president`, ADD `president_reject_comment` VARCHAR(255) NULL AFTER `president_date`, ADD INDEX (`president`); 

ALTER TABLE `member_loans` ADD CONSTRAINT `member_loans_foreign_key_9` FOREIGN KEY (`president`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 


-- 2025-01-26
ALTER TABLE `member_loan_settings` ADD `installment` DOUBLE NULL AFTER `late_fee`, ADD `saving` DOUBLE NULL AFTER `installment`; 


-- 2025-01-26 B
ALTER TABLE `loan_type` ADD `is_top_up` BOOLEAN NULL AFTER `late_fee`, ADD `is_emergeny` BOOLEAN NULL AFTER `is_top_up`, ADD `percentage_before_top_op` INT NULL AFTER `is_emergeny`; 

UPDATE `loan_type` SET `is_top_up` = '1', `is_emergeny` = '0' WHERE `loan_type`.`id` = 1; 
UPDATE `loan_type` SET `is_top_up` = '0', `is_emergeny` = '1' WHERE `loan_type`.`id` = 2; 
UPDATE `loan_type` SET `is_top_up` = '1', `is_emergeny` = '0' WHERE `loan_type`.`id` = 3; 
UPDATE `loan_type` SET `is_top_up` = '0', `is_emergeny` = '0', `percentage_before_top_op` = '50' WHERE `loan_type`.`id` = 4; 
UPDATE `loan_type` SET `is_top_up` = '0', `is_emergeny` = '0', `percentage_before_top_op` = '50' WHERE `loan_type`.`id` = 6; 

-- 2025-01-30
INSERT INTO `member` (`id`, `fname`, `lname`, `phone_cell`, `id_number`, `id_location`, `email`, `birth_date`, `civil_status`, `sex`, `address`, `village_id`, `employment_status`, `company`, `income`, `age`, `job_title`, `mi`, `rdate`, `Account_balance`, `is_new`, `member_fee`, `status`) VALUES (NULL, 'EMERGENCY', 'SIGNER', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, '0');
INSERT INTO `users` (`id`, `username`, `phone`, `email`, `password`, `comfirm`, `trn_date`, `name`, `photo`, `signature`, `about`, `Position`, `staff_id`, `member_acc`, `status`) VALUES (NULL, 'EMERGENCY', 'EMERGENCY', 'EMERGENCY', '', '', current_timestamp(), 'EMERGENCY', NULL, NULL, NULL, '5', NULL, NULL, '0');
UPDATE users SET member_acc = (SELECT id FROM member WHERE fname='EMERGENCY' AND lname='SIGNER') WHERE username='EMERGENCY';


-- 2025-02-02
CREATE TABLE `emergency_details` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `loan_id` INT(11) NOT NULL , `approved_amount` DOUBLE NOT NULL , PRIMARY KEY (`id`), INDEX (`loan_id`)) ENGINE = InnoDB; 
ALTER TABLE `emergency_details` ADD CONSTRAINT `emergency_details_foreign_key_1` FOREIGN KEY (`loan_id`) REFERENCES `member_loans`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

-- 2025-02-03
ALTER TABLE `saving` ADD `overdue_id` INT(11) NULL AFTER `member_id`, ADD INDEX (`overdue_id`); 
ALTER TABLE `saving` CHANGE `overdue_id` `overdue_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL; 
ALTER TABLE `saving` ADD CONSTRAINT `saving_foreign_key_2` FOREIGN KEY (`overdue_id`) REFERENCES `overdue_settings`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

CREATE TABLE `bank_slip_requests` (`id` INT NOT NULL AUTO_INCREMENT , `type` VARCHAR(255) NOT NULL , `ref_number` VARCHAR(255) NOT NULL , `amount` DOUBLE NOT NULL , `paid_at` DATE NOT NULL , `created_at` DATETIME NOT NULL , PRIMARY KEY (`id`), UNIQUE (`ref_number`)) ENGINE = InnoDB; 
ALTER TABLE `bank_slip_requests` ADD `data` TEXT NOT NULL AFTER `amount`; 

ALTER TABLE `bank_slip_requests` ADD `member_id` INT(11) NOT NULL AFTER `id`, ADD INDEX (`member_id`); 
ALTER TABLE `bank_slip_requests` ADD CONSTRAINT `bank_slip_requests_foreign_key_1` FOREIGN KEY (`member_id`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

-- 2025-02-05
CREATE TABLE `topup_details` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `loan_id` INT(11) NOT NULL , `toped_up_loan_id` INT(11) NOT NULL , `recovered_from_id` INT(11) NOT NULL , `total_recovered_amount` DOUBLE NOT NULL , PRIMARY KEY (`id`), INDEX (`loan_id`), INDEX (`toped_up_loan_id`), INDEX (`recovered_from_id`)) ENGINE = InnoDB; 
ALTER TABLE `topup_details` ADD CONSTRAINT `topup_details_foreign_key_1` FOREIGN KEY (`loan_id`) REFERENCES `member_loans`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `topup_details` ADD CONSTRAINT `topup_details_foreign_key_2` FOREIGN KEY (`toped_up_loan_id`) REFERENCES `member_loans`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `topup_details` ADD CONSTRAINT `topup_details_foreign_key_3` FOREIGN KEY (`recovered_from_id`) REFERENCES `lend_payments`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 
ALTER TABLE `topup_details` CHANGE `toped_up_loan_id` `toped_up_loan_id` INT(11) NULL, CHANGE `recovered_from_id` `recovered_from_id` INT(11) NULL; 


-- 2025-02-09
ALTER TABLE `lend_payments` ADD `comment` VARCHAR(255) NULL AFTER `overdue_fine`; 
ALTER TABLE `interest` CHANGE `loan_ref` `loan_ref` INT(11) NULL DEFAULT NULL AFTER `loan_interest`, CHANGE `fine_overdue` `fine_overdue` INT(11) NULL DEFAULT NULL AFTER `lend_payment_id`, CHANGE `saving_overdu` `saving_overdu` INT(11) NULL DEFAULT NULL AFTER `saving_id`;

-- 2025-02-15
ALTER TABLE `bank_slip_requests` ADD `status` VARCHAR(255) NOT NULL DEFAULT 'Open' AFTER `paid_at`; 
ALTER TABLE `bank_slip_requests` ADD `comment` VARCHAR(255) NULL AFTER `status`; 

-- 2025-02-16
ALTER TABLE `saving` ADD `comment` VARCHAR(255) NULL AFTER `fine`;

-- 2025-03-23
ALTER TABLE `bank_slip_requests` ADD `has_fine` BOOLEAN NULL AFTER `comment`, ADD `fine_data` TEXT NULL AFTER `has_fine`; 


-- 2025-03-26
CREATE TABLE `fine_types` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `default_amount` DOUBLE NOT NULL , PRIMARY KEY (`id`), UNIQUE (`name`)) ENGINE = InnoDB; 
CREATE TABLE `special_fines` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `member_id` INT(11) NOT NULL , `fine_type_id` BIGINT UNSIGNED NOT NULL , `user_id` INT(11) NOT NULL , `fine_amount` DOUBLE NOT NULL , `date` DATETIME NOT NULL , `status` VARCHAR(255) NOT NULL DEFAULT 'Active' , `reference_number` VARCHAR(255) NULL , PRIMARY KEY (`id`), INDEX (`member_id`), INDEX (`fine_type_id`), INDEX (`user_id`)) ENGINE = InnoDB; 
ALTER TABLE `special_fines` ADD CONSTRAINT `special_fine_foreign_key_1` FOREIGN KEY (`member_id`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `special_fines` ADD CONSTRAINT `special_fine_foreign_key_2` FOREIGN KEY (`fine_type_id`) REFERENCES `fine_types`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `special_fines` ADD CONSTRAINT `special_fine_foreign_key_3` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

INSERT INTO `fine_types` (`id`, `name`, `default_amount`) VALUES (NULL, 'Non Vote Fines', '2000'), (NULL, 'Meeting Absance Fines', '2000');


-- 2025-03-29
CREATE TABLE `shared_interest` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `total_investment` DOUBLE NOT NULL , `total_interest` DOUBLE NOT NULL , `year` INT NOT NULL , `month` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB; 
CREATE TABLE `shared_interest_histories` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `member_id` INT(11) NOT NULL , `shared_interest_id` BIGINT UNSIGNED NOT NULL , `amount` DOUBLE NOT NULL , `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`), INDEX (`member_id`), INDEX (`shared_interest_id`)) ENGINE = InnoDB; 
ALTER TABLE `shared_interest_histories` ADD CONSTRAINT `shared_interest_histories_foreign_key_1` FOREIGN KEY (`member_id`) REFERENCES `member`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `shared_interest_histories` ADD CONSTRAINT `shared_interest_histories_foreign_key_2` FOREIGN KEY (`shared_interest_id`) REFERENCES `shared_interest`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

ALTER TABLE `shared_interest` ADD `interest_rate` DOUBLE NOT NULL AFTER `total_interest`; 

UPDATE interest SET loan_interest=NULL WHERE loan_interest=0 AND year=2024 AND month=1;
UPDATE interest SET fine_overdue=NULL WHERE fine_overdue=0 AND year=2024 AND month=1;
UPDATE interest SET saving_overdu=NULL WHERE saving_overdu=0 AND year=2024 AND month=1;
UPDATE interest SET membership_fee=NULL WHERE membership_fee=0 AND year=2024 AND month=1;
UPDATE interest SET amount=COALESCE(loan_interest, fine_overdue, saving_overdu, membership_fee) WHERE amount IS NULL AND year=2024 AND month=1;

UPDATE interest SET loan_interest=NULL WHERE loan_interest=0 AND year=2024 AND month=2;
UPDATE interest SET fine_overdue=NULL WHERE fine_overdue=0 AND year=2024 AND month=2;
UPDATE interest SET saving_overdu=NULL WHERE saving_overdu=0 AND year=2024 AND month=2;
UPDATE interest SET membership_fee=NULL WHERE membership_fee=0 AND year=2024 AND month=2;
UPDATE interest SET amount=COALESCE(loan_interest, fine_overdue, saving_overdu, membership_fee) WHERE amount IS NULL AND year=2024 AND month=2;

UPDATE interest SET Is_posted=1 WHERE year=2024;
-- 2025-04-07
ALTER TABLE `overdue_settings` ADD `bank_slip_overdue` DATE NULL AFTER `payment_overdue`, ADD `social_overdue` DATE NULL AFTER `bank_slip_overdue`; 

-- 2025-04-09
ALTER TABLE `loan_type` ADD `special_limit` INT NULL AFTER `percentage_before_top_op`; 

-- 2025-04-16
CREATE TABLE `loan_remainder` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `loan_id` INT(11) NOT NULL , `amount` INT NOT NULL , PRIMARY KEY (`id`), INDEX (`loan_id`)) ENGINE = InnoDB; 
ALTER TABLE `loan_remainder` ADD CONSTRAINT `loan_remainder_foreign_key_1` FOREIGN KEY (`loan_id`) REFERENCES `member_loans`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT; 

-- 2025-04-19
UPDATE saving SET month=2 WHERE month=3 AND year=2025 and id IN(4858, 4860, 4855, 4887, 4861, 4863, 4851, 4859, 4895, 4867, 4891, 4852, 4847, 4896, 4853, 4848, 4894, 4849);


-- 2025-05-03
ALTER TABLE `bank_slip_requests` CHANGE `ref_number` `ref_number` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL; 
ALTER TABLE `bank_slip_requests` ADD `ref_copy` VARCHAR(255) NULL AFTER `ref_number`; 



