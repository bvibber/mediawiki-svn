ALTER TABLE `%dc%_option_attribute_values` 
	ADD INDEX `versioned_end_object` (`remove_transaction_id`, `object_id`, `option_id`, `value_id`),
	ADD INDEX `versioned_end_option` (`remove_transaction_id`, `option_id`, `object_id`, `value_id`),
	ADD INDEX `versioned_end_value` (`remove_transaction_id`, `value_id`),
	ADD INDEX `versioned_start_object` (`add_transaction_id`, `object_id`, `option_id`, `value_id`),
	ADD INDEX `versioned_start_option` (`add_transaction_id`, `option_id`, `object_id`, `value_id`),
	ADD INDEX `versioned_start_value` (`add_transaction_id`, `value_id`);
	
--	ADD INDEX `unversioned_object` (`remove_transaction_id`, `object_id`, `option_id`, `value_id`),
--	ADD INDEX `unversioned_option` (`remove_transaction_id`, `option_id`, `object_id`, `value_id`),
--	ADD INDEX `unversioned_value` (`remove_transaction_id`, `value_id`);