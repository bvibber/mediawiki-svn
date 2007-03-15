ALTER TABLE `uw_syntrans` 
	ADD INDEX `versioned_syntrans` (`remove_transaction_id`, `syntrans_sid`),
	ADD INDEX `versioned_expression` (`remove_transaction_id`, `expression_id`, `identical_meaning`, `defined_meaning_id`),
	ADD INDEX `versioned_defined_meaning` (`remove_transaction_id`, `defined_meaning_id`, `identical_meaning`, `expression_id`);

--	ADD INDEX `unversioned_syntrans` (`syntrans_sid`),
--	ADD INDEX `unversioned_expression` (`expression_id`, `identical_meaning`, `defined_meaning_id`),
--	ADD INDEX `unversioned_defined_meaning` (`defined_meaning_id`, `identical_meaning`, `expression_id`);
	