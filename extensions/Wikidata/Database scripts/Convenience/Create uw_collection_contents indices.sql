ALTER TABLE `uw_collection_contents` 
	ADD INDEX `versioned_end_collection` (`remove_transaction_id`, `collection_id`, `member_mid`),
	ADD INDEX `versioned_end_collection_member` (`remove_transaction_id`, `member_mid`, `collection_id`),
	ADD INDEX `versioned_end_internal_id` (`remove_transaction_id`, `internal_member_id` (255), `collection_id`, `member_mid`),
	ADD INDEX `versioned_start_collection` (`add_transaction_id`, `collection_id`, `member_mid`),
	ADD INDEX `versioned_start_collection_member` (`add_transaction_id`, `member_mid`, `collection_id`),
	ADD INDEX `versioned_start_internal_id` (`add_transaction_id`, `internal_member_id` (255), `collection_id`, `member_mid`),	
	ADD INDEX `collection_id_idx` (`collection_id`),
	ADD INDEX `member_mid` (`collection_id`);
--	ADD INDEX `unversioned_collection` (`collection_id`, `member_mid`),
--	ADD INDEX `unversioned_collection_member` (`member_mid`, `collection_id`),
--	ADD INDEX `unversioned_internal_id` (`internal_member_id` (255), `collection_id`, `member_mid`);
