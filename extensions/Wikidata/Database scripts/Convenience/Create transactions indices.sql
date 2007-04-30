ALTER TABLE `transactions` 
	ADD INDEX `user` (`user_id`, `transaction_id`);
