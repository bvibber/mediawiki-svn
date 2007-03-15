ALTER TABLE `uw_alt_meaningtexts` 
	ADD INDEX `versioned_meaning` (`remove_transaction_id`, `meaning_mid`, `meaning_text_tcid`, `source_id`),
	ADD INDEX `versioned_text` (`remove_transaction_id`, `meaning_text_tcid`, `meaning_mid`, `source_id`),
	ADD INDEX `versioned_source` (`remove_transaction_id`, `source_id`, `meaning_mid`, `meaning_text_tcid`);
	
--	ADD INDEX `unversioned_meaning` (`meaning_mid`, `meaning_text_tcid`, `source_id`),
--	ADD INDEX `unversioned_text` (`meaning_text_tcid`, `meaning_mid`, `source_id`),
--	ADD INDEX `unversioned_source` (`source_id`, `meaning_mid`, `meaning_text_tcid`);