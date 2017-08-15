CREATE TABLE `bar_user` (
	`user_id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_username` VARCHAR(64) NOT NULL,
	`user_pass_hash` VARCHAR(64) NOT NULL,
	`user_hash_salt` VARCHAR(128) NOT NULL,
	`user_create_datetime` DATETIME NOT NULL,
	`user_create_ip` TEXT NOT NULL,
	`user_name_full` VARCHAR(128) NOT NULL,
	`user_primary_mail_id` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`user_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
