CREATE TABLE `bar_mail_verification` (
	`mail_ver_id` INT(11) NOT NULL AUTO_INCREMENT,
	`mail_ver_user_id` INT(11) NOT NULL,
	`mail_ver_mail` VARCHAR(256) NOT NULL,
	`mail_ver_key` VARCHAR(32) NOT NULL,
	`mail_ver_create_datetime` DATETIME NOT NULL,
	`mail_ver_previous_mail_id` INT(11) NULL DEFAULT NULL,
	`mail_ver_expire_datetime` DATETIME NOT NULL,
	PRIMARY KEY (`mail_ver_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
