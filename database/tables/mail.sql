CREATE TABLE `bar_mail` (
	`mail_id` INT(11) NOT NULL AUTO_INCREMENT,
	`mail_user_id` INT(11) NOT NULL,
	`mail_mail` VARCHAR(256) NOT NULL,
	`mail_create_datetime` DATETIME NOT NULL,
	`mail_verified_datetime` DATETIME NULL DEFAULT NULL,
	`mail_verified_ip` TEXT NULL,
	PRIMARY KEY (`mail_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=2
;
