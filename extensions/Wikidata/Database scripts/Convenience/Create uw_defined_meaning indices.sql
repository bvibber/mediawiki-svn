ALTER TABLE `%dc%_defined_meaning` 
	ADD INDEX `versioned_end_meaning` (`remove_transaction_id`, `defined_meaning_id`, `expression_id`),
	ADD INDEX `versioned_end_expression` (`remove_transaction_id`, `expression_id`, `defined_meaning_id`),
	ADD INDEX `versioned_end_meaning_text` (`remove_transaction_id`, `meaning_text_tcid`, `defined_meaning_id`),
	ADD INDEX `versioned_start_meaning` (`add_transaction_id`, `defined_meaning_id`, `expression_id`),
	ADD INDEX `versioned_start_expression` (`add_transaction_id`, `expression_id`, `defined_meaning_id`),
	ADD INDEX `versioned_start_meaning_text` (`add_transaction_id`, `meaning_text_tcid`, `defined_meaning_id`),
	ADD INDEX `defining_expression_idx` (`defined_meaning_id`,`expression_id`),
	ADD INDEX `defined_meaning_idx` (`defined_meaning_id`);
--	ADD INDEX `unversioned_meaning` (`defined_meaning_id`, `expression_id`),
--	ADD INDEX `unversioned_expression` (`expression_id`, `defined_meaning_id`),
--	ADD INDEX `unversioned_meaning_text` (`meaning_text_tcid`, `defined_meaning_id`);
