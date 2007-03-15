ALTER TABLE `uw_url_attribute_values` 
	ADD INDEX `versioned_object` (`remove_transaction_id`, `object_id`, `attribute_mid`, `value_id`),
	ADD INDEX `versioned_attribute` (`remove_transaction_id`, `attribute_mid`, `object_id`, `value_id`),
	ADD INDEX `versioned_value` (`remove_transaction_id`, `value_id`);
	
--	ADD INDEX `unversioned_object` (`object_id`, `attribute_mid`, `value_id`),
--	ADD INDEX `unversioned_attribute` (`attribute_mid`, `object_id`, `value_id`),
--	ADD INDEX `unversioned_value` (`value_id`);