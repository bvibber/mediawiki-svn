ALTER TABLE `%dc%_bootstrapped_defined_meanings` 
	ADD INDEX `unversioned_meaning` (`defined_meaning_id`),
	ADD INDEX `unversioned_name` (`name` (255), `defined_meaning_id`);
