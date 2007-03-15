ALTER TABLE `uw_class_attributes` 
	ADD INDEX `versioned_class` (`remove_transaction_id`, `class_mid`, `attribute_mid`, `object_id`),
	ADD INDEX `versioned_attribute` (`remove_transaction_id`, `attribute_mid`, `class_mid`, `object_id`),
	ADD INDEX `versioned_object` (`remove_transaction_id`, `object_id`);

--	ADD INDEX `unversioned_class` (`class_mid`, `attribute_mid`, `object_id`),
--	ADD INDEX `unversioned_attribute` (`attribute_mid`, `class_mid`, `object_id`),
--	ADD INDEX `unversioned_object` (`object_id`);