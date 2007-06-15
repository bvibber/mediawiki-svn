ALTER TABLE `%dc%_transactions` 
	ADD INDEX `user` (`user_id`, `transaction_id`);
