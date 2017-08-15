CREATE TABLE `bar_session` (
	`session_id` INT(11) NOT NULL AUTO_INCREMENT,
	`session_user_id` INT(11) NOT NULL,
	`session_key` VARCHAR(64) NOT NULL,
	`session_create_datetime` DATETIME NOT NULL,
	`session_create_ip` TEXT NOT NULL,
	`session_expire_datetime` DATETIME NOT NULL,
	PRIMARY KEY (`session_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
