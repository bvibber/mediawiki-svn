ALTER TABLE `uw_defined_meaning` 
	ADD INDEX `versioned_meaning` (`remove_transaction_id`, `defined_meaning_id`, `expression_id`),
	ADD INDEX `versioned_expression` (`remove_transaction_id`, `expression_id`, `defined_meaning_id`),
	ADD INDEX `versioned_meaning_text` (`remove_transaction_id`, `meaning_text_tcid`, `defined_meaning_id`);
	
--	ADD INDEX `unversioned_meaning` (`defined_meaning_id`, `expression_id`),
--	ADD INDEX `unversioned_expression` (`expression_id`, `defined_meaning_id`),
--	ADD INDEX `unversioned_meaning_text` (`meaning_text_tcid`, `defined_meaning_id`);