ALTER TABLE `%dc%_text_attribute_values` 
	ADD INDEX `versioned_end_object` (`remove_transaction_id`, `object_id`, `attribute_mid`, `value_id`),
	ADD INDEX `versioned_end_attribute` (`remove_transaction_id`, `attribute_mid`, `object_id`, `value_id`),
	ADD INDEX `versioned_end_value` (`remove_transaction_id`, `value_id`),
	ADD INDEX `versioned_start_object` (`add_transaction_id`, `object_id`, `attribute_mid`, `value_id`),
	ADD INDEX `versioned_start_attribute` (`add_transaction_id`, `attribute_mid`, `object_id`, `value_id`),
	ADD INDEX `versioned_start_value` (`add_transaction_id`, `value_id`);
	
--	ADD INDEX `unversioned_object` (`object_id`, `attribute_mid`, `value_id`),
--	ADD INDEX `unversioned_attribute` (`attribute_mid`, `object_id`, `value_id`),
--	ADD INDEX `unversioned_value` (`value_id`);