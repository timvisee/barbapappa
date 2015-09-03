CREATE TABLE `bar_transactions` (
	`transaction_id` INT(11) NOT NULL AUTO_INCREMENT,
	`transaction_user_id` INT(11) NOT NULL,
	`transaction_amount` INT(11) NOT NULL,
	`transaction_datetime` DATETIME NOT NULL,
	PRIMARY KEY (`transaction_id`)
)
ENGINE=InnoDB
;
