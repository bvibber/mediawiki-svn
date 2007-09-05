ALTER TABLE `%dc%_alt_meaningtexts` 
	ADD INDEX `versioned_end_meaning` (`remove_transaction_id`, `meaning_mid`, `meaning_text_tcid`, `source_id`),
	ADD INDEX `versioned_end_text` (`remove_transaction_id`, `meaning_text_tcid`, `meaning_mid`, `source_id`),
	ADD INDEX `versioned_end_source` (`remove_transaction_id`, `source_id`, `meaning_mid`, `meaning_text_tcid`),
	ADD INDEX `versioned_start_meaning` (`add_transaction_id`, `meaning_mid`, `meaning_text_tcid`, `source_id`),
	ADD INDEX `versioned_start_text` (`add_transaction_id`, `meaning_text_tcid`, `meaning_mid`, `source_id`),
	ADD INDEX `versioned_start_source` (`add_transaction_id`, `source_id`, `meaning_mid`, `meaning_text_tcid`);
	
--	ADD INDEX `unversioned_meaning` (`meaning_mid`, `meaning_text_tcid`, `source_id`),
--	ADD INDEX `unversioned_text` (`meaning_text_tcid`, `meaning_mid`, `source_id`),
--	ADD INDEX `unversioned_source` (`source_id`, `meaning_mid`, `meaning_text_tcid`);