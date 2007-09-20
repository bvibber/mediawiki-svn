ALTER TABLE `%dc%_collection` 
	ADD INDEX `versioned_end_collection` (`remove_transaction_id`, `collection_id`, `collection_mid`),
	ADD INDEX `versioned_end_collection_meaning` (`remove_transaction_id`, `collection_mid`, `collection_id`),
	ADD INDEX `versioned_end_collection_type` (`remove_transaction_id`, `collection_type` (4), `collection_id`, `collection_mid`),
	ADD INDEX `versioned_start_collection` (`add_transaction_id`, `collection_id`, `collection_mid`),
	ADD INDEX `versioned_start_collection_meaning` (`add_transaction_id`, `collection_mid`, `collection_id`),
	ADD INDEX `versioned_start_collection_type` (`add_transaction_id`, `collection_type` (4), `collection_id`, `collection_mid`);
	
--	ADD INDEX `unversioned_collection` (`collection_id`, `collection_mid`),
--	ADD INDEX `unversioned_collection_meaning` (`collection_mid`, `collection_id`),
--	ADD INDEX `unversioned_collection_type` (`collection_type` (4), `collection_id`, `collection_mid`);