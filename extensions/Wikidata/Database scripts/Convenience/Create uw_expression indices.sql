ALTER TABLE `%dc%_expression` 
	ADD INDEX `versioned_end_expression` (`remove_transaction_id`, `expression_id`, `language_id`),
	ADD INDEX `versioned_end_language` (`remove_transaction_id`, `language_id`, `expression_id`),
	ADD INDEX `versioned_end_spelling` (`remove_transaction_id`, `spelling` (255), `expression_id`, `language_id`),
	ADD INDEX `versioned_start_expression` (`add_transaction_id`, `expression_id`, `language_id`),
	ADD INDEX `versioned_start_language` (`add_transaction_id`, `language_id`, `expression_id`),
	ADD INDEX `versioned_start_spelling` (`add_transaction_id`, `spelling` (255), `expression_id`, `language_id`),
	ADD INDEX `expressions_unique_idx` (`expression_id`,`language_id`),
	ADD INDEX `expressions_idx`	(`expression_id`),
	ADD INDEX `language_idx`	(`language_id`)
	;
--	ADD INDEX `unversioned_spelling` (`spelling` (255), `expression_id`, `language_id`),
--	ADD INDEX `unversioned_expression` (`expression_id`, `language_id`),
--	ADD INDEX `unversioned_language` (`language_id`, `expression_id`);
	
