CREATE TABLE `bar_users_meta` (
	`user_meta_id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_meta_user_id` INT(11) NOT NULL,
	`user_meta_key` VARCHAR(64) NOT NULL,
	`user_meta_value` TEXT NOT NULL,
	PRIMARY KEY (`user_meta_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
