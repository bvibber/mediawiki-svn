ALTER TABLE `uw_meaning_relations` 
	ADD INDEX `versioned_outgoing` (`remove_transaction_id`, `meaning1_mid`, `relationtype_mid`, `meaning2_mid`),
	ADD INDEX `versioned_incoming` (`remove_transaction_id`, `meaning2_mid`, `relationtype_mid`, `meaning1_mid`),
	ADD INDEX `versioned_relation` (`remove_transaction_id`, `relation_id`);
	
--	ADD INDEX `unversioned_outgoing` (`meaning1_mid`, `relationtype_mid`, `meaning2_mid`),
--	ADD INDEX `unversioned_incoming` (`meaning2_mid`, `relationtype_mid`, `meaning1_mid`),
--	ADD INDEX `unversioned_relation` (`relation_id`);
	