ALTER TABLE `%dc%_option_attribute_options` 
	ADD INDEX `versioned_end_option` (`remove_transaction_id`, `option_mid`, `attribute_id`, `option_id`),
	ADD INDEX `versioned_end_attribute` (`remove_transaction_id`, `attribute_id`, `option_id`, `option_mid`),
	ADD INDEX `versioned_end_id` (`remove_transaction_id`, `option_id`),
	ADD INDEX `versioned_start_option` (`add_transaction_id`, `option_mid`, `attribute_id`, `option_id`),
	ADD INDEX `versioned_start_attribute` (`add_transaction_id`, `attribute_id`, `option_id`, `option_mid`),
	ADD INDEX `versioned_start_id` (`add_transaction_id`, `option_id`);
	
--	ADD INDEX `unversioned_option` (`option_mid`, `attribute_id`, `option_id`),
--	ADD INDEX `unversioned_attribute` (`attribute_id`, `option_id`, `option_mid`),
--	ADD INDEX `unversioned_id` (`option_id`);