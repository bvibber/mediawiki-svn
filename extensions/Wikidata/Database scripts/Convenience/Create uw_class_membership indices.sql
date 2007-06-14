ALTER TABLE `%dc%_class_membership` 
	ADD INDEX `versioned_end_class` (`remove_transaction_id`, `class_mid`, `class_member_mid`),
	ADD INDEX `versioned_end_class_member` (`remove_transaction_id`, `class_member_mid`, `class_mid`),
	ADD INDEX `versioned_end_class_membership` (`remove_transaction_id`, `class_membership_id`),
	ADD INDEX `versioned_start_class` (`add_transaction_id`, `class_mid`, `class_member_mid`),
	ADD INDEX `versioned_start_class_member` (`add_transaction_id`, `class_member_mid`, `class_mid`),
	ADD INDEX `versioned_start_class_membership` (`add_transaction_id`, `class_membership_id`);
	
--	ADD INDEX `unversioned_class` (`class_mid`, `class_member_mid`),
--	ADD INDEX `unversioned_class_member` (`class_member_mid`, `class_mid`),
--	ADD INDEX `unversioned_class_membership` (`class_membership_id`);