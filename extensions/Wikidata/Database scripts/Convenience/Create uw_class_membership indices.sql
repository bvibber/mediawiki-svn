ALTER TABLE `uw_class_membership` 
	ADD INDEX `versioned_class` (`remove_transaction_id`, `class_mid`, `class_member_mid`),
	ADD INDEX `versioned_class_member` (`remove_transaction_id`, `class_member_mid`, `class_mid`),
	ADD INDEX `versioned_class_membership` (`remove_transaction_id`, `class_membership_id`)
	
--	ADD INDEX `unversioned_class` (`class_mid`, `class_member_mid`),
--	ADD INDEX `unversioned_class_member` (`class_member_mid`, `class_mid`),
--	ADD INDEX `unversioned_class_membership` (`class_membership_id`);