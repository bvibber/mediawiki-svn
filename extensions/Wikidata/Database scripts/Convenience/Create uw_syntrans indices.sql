ALTER TABLE `uw_syntrans` 
	ADD INDEX `versioned_end_syntrans` (`remove_transaction_id`, `syntrans_sid`),
	ADD INDEX `versioned_end_expression` (`remove_transaction_id`, `expression_id`, `identical_meaning`, `defined_meaning_id`),
	ADD INDEX `versioned_end_defined_meaning` (`remove_transaction_id`, `defined_meaning_id`, `identical_meaning`, `expression_id`),
	ADD INDEX `versioned_start_syntrans` (`add_transaction_id`, `syntrans_sid`),
	ADD INDEX `versioned_start_expression` (`add_transaction_id`, `expression_id`, `identical_meaning`, `defined_meaning_id`),
	ADD INDEX `versioned_start_defined_meaning` (`add_transaction_id`, `defined_meaning_id`, `identical_meaning`, `expression_id`),
	ADD INDEX `syntrans_defined_meaning_idx`	(`defined_meaning_id`),
	ADD INDEX `syntrans_expression_id_idx`	(`expression_id`),
	ADD INDEX `syntrans_remove_transaction_idx`	(`remove_transaction_id`);
	
--	ADD INDEX `unversioned_syntrans` (`syntrans_sid`),
--	ADD INDEX `unversioned_expression` (`expression_id`, `identical_meaning`, `defined_meaning_id`),
--	ADD INDEX `unversioned_defined_meaning` (`defined_meaning_id`, `identical_meaning`, `expression_id`);
	
