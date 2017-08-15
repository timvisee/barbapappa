CREATE TABLE `bar_balance` (
	`balance_id` INT(11) NOT NULL AUTO_INCREMENT,
	`balance_user_id` INT(11) NOT NULL,
	`balance_amount` INT(11) NOT NULL DEFAULT '0',
	`balance_modified_datetime` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`balance_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
