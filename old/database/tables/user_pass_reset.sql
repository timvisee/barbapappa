CREATE TABLE `bar_user_pass_reset` (
	`reset_id` INT(11) NOT NULL AUTO_INCREMENT,
	`reset_user_id` INT(11) NOT NULL,
	`reset_key` VARCHAR(32) NOT NULL,
	`reset_creation_datetime` DATETIME NOT NULL,
	`reset_creation_ip` TEXT NULL,
	`reset_expiration_datetime` DATETIME NOT NULL,
	PRIMARY KEY (`reset_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
