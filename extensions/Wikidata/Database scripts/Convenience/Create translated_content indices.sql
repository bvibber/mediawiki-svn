ALTER TABLE `translated_content` 
	ADD INDEX `versioned_end_translated_content` (`remove_transaction_id`, `translated_content_id`, `language_id`, `text_id`),
	ADD INDEX `versioned_end_text` (`remove_transaction_id`, `text_id`, `translated_content_id`, `language_id`),
	ADD INDEX `versioned_start_translated_content` (`add_transaction_id`, `translated_content_id`, `language_id`, `text_id`),
	ADD INDEX `versioned_start_text` (`add_transaction_id`, `text_id`, `translated_content_id`, `language_id`);

--	ADD INDEX `unversioned_translated_content` (`translated_content_id`, `language_id`, `text_id`),
--	ADD INDEX `unversioned_text` (`text_id`, `translated_content_id`, `language_id`);