ALTER TABLE `%dc%_meaning_relations` 
	ADD INDEX `versioned_end_outgoing` (`remove_transaction_id`, `meaning1_mid`, `relationtype_mid`, `meaning2_mid`),
	ADD INDEX `versioned_end_incoming` (`remove_transaction_id`, `meaning2_mid`, `relationtype_mid`, `meaning1_mid`),
	ADD INDEX `versioned_end_relation` (`remove_transaction_id`, `relation_id`),
	ADD INDEX `versioned_start_outgoing` (`add_transaction_id`, `meaning1_mid`, `relationtype_mid`, `meaning2_mid`),
	ADD INDEX `versioned_start_incoming` (`add_transaction_id`, `meaning2_mid`, `relationtype_mid`, `meaning1_mid`),
	ADD INDEX `versioned_start_relation` (`add_transaction_id`, `relation_id`);
	
--	ADD INDEX `unversioned_outgoing` (`meaning1_mid`, `relationtype_mid`, `meaning2_mid`),
--	ADD INDEX `unversioned_incoming` (`meaning2_mid`, `relationtype_mid`, `meaning1_mid`),
--	ADD INDEX `unversioned_relation` (`relation_id`);
	