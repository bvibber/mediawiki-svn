ALTER TABLE `uw_option_attribute_values` 
	ADD INDEX `versioned_object` (`remove_transaction_id`, `object_id`, `option_id`, `value_id`),
	ADD INDEX `versioned_option` (`remove_transaction_id`, `option_id`, `object_id`, `value_id`),
	ADD INDEX `versioned_value` (`remove_transaction_id`, `value_id`);
	
--	ADD INDEX `unversioned_object` (`remove_transaction_id`, `object_id`, `option_id`, `value_id`),
--	ADD INDEX `unversioned_option` (`remove_transaction_id`, `option_id`, `object_id`, `value_id`),
--	ADD INDEX `unversioned_value` (`remove_transaction_id`, `value_id`);