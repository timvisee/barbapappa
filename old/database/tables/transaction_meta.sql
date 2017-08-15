CREATE TABLE `bar_transaction_meta` (
	`transaction_meta_id` INT(11) NOT NULL AUTO_INCREMENT,
	`transaction_meta_transaction_id` INT(11) NOT NULL,
	`transaction_meta_key` VARCHAR(64) NOT NULL,
	`transaction_meta_value` TEXT NOT NULL,
	PRIMARY KEY (`transaction_meta_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
